<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DocumentController extends Controller
{
    public function index(): View
    {
        $this->syncDataDirectoryRecords();

        return view('admin.documents.index', [
            'documents' => Document::query()->latest()->get(),  // CompanyScope auto-filters by company
        ]);
    }

    public function consult(): View
    {
        $this->syncDataDirectoryRecords();

        return view('admin.documents.consult', [
            'documents' => Document::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        @set_time_limit(300);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,txt,md,jpg,jpeg,png,gif,webp', 'max:51200'],
        ]);

        $companyId = (string) $request->user()->company_id;
        $projectRoot = dirname(base_path());
        $companyDir = 'data/company_'.$companyId;
        $targetDir = $projectRoot.'/'.$companyDir;

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $file = $data['file'];
        $extension = Str::lower($file->getClientOriginalExtension() ?: 'bin');
        $filename = now()->format('YmdHis').'_'.$this->slugFilename($data['title']).'.'.$extension;
        $file->move($targetDir, $filename);

        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $isImage = in_array($extension, $imageExts, true);

        $description = null;
        if ($isImage) {
            try {
                $description = $this->describeImage($targetDir.'/'.$filename, $projectRoot);
            } catch (\Throwable) {
                $description = null;
            }
        }

        Document::query()->create([
            'title'       => $data['title'],
            'file_path'   => $companyDir.'/'.$filename,
            'type'        => $extension,
            'description' => $description,
            'company_id'  => $companyId,
        ]);

        $this->syncDataDirectoryRecords($companyId);

        try {
            $summary = $this->runReindex($companyId);
        } catch (ProcessFailedException $exception) {
            $errorOutput = trim($exception->getProcess()->getErrorOutput());
            $fallbackOutput = trim($exception->getProcess()->getOutput());

            return back()->withErrors([
                'process' => 'Document uploaded but indexing failed: '.($errorOutput ?: $fallbackOutput),
            ]);
        } catch (\RuntimeException $exception) {
            return back()->withErrors([
                'process' => 'Document uploaded but indexing failed: '.$exception->getMessage(),
            ]);
        }

        $status = 'Document uploaded and indexed successfully.';
        if ($summary !== '') {
            $lines = preg_split('/\R/', $summary) ?: [];
            $lastLine = trim(end($lines) ?: '');
            if ($lastLine !== '') {
                $status .= ' '.$lastLine;
            }
        }

        return back()->with('status', $status);
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        @set_time_limit(300);

        $companyId = (string) $request->user()->company_id;

        // Safety check: document must belong to this company (CompanyScope covers queries,
        // but route model binding bypasses it, so we enforce it explicitly here).
        if ((string) $document->company_id !== $companyId) {
            abort(403);
        }

        // Delete the physical file.
        $projectRoot = dirname(base_path());
        $absolutePath = $projectRoot . '/' . $document->file_path;
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        // Delete the DB record.
        $document->delete();

        // Rebuild the FAISS index for this company from the remaining files.
        // FAISS does not support removing individual vectors, so a full rebuild is required.
        $remainingDocs = Document::query()->where('company_id', $companyId)->count();
        if ($remainingDocs > 0) {
            try {
                $this->runReindex($companyId);
            } catch (ProcessFailedException $exception) {
                return redirect()->route('admin.documents.index')->withErrors([
                    'process' => 'Document deleted but re-indexing failed: '
                        . (trim($exception->getProcess()->getErrorOutput()) ?: trim($exception->getProcess()->getOutput())),
                ]);
            } catch (\RuntimeException $exception) {
                return redirect()->route('admin.documents.index')->withErrors([
                    'process' => 'Document deleted but re-indexing failed: ' . $exception->getMessage(),
                ]);
            }
        } else {
            // No documents left — remove the stale index files so queries return "no documents" cleanly.
            $companyDir = $projectRoot . '/data/company_' . $companyId;
            foreach (['faiss.index', 'faiss.meta.json'] as $indexFile) {
                $path = $companyDir . '/' . $indexFile;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        return redirect()->route('admin.documents.index')
            ->with('status', 'Document deleted and vector index updated.');
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $companyId = (string) $request->user()->company_id;

        // Delete all DB records (CompanyScope ensures only this company's rows are touched).
        Document::query()->delete();

        // Delete the company's data directory (files + FAISS index).
        $projectRoot = dirname(base_path());
        $companyDir  = $projectRoot . '/data/company_' . $companyId;

        if (is_dir($companyDir)) {
            File::deleteDirectory($companyDir);
        }

        return redirect()->route('admin.documents.index')
            ->with('status', 'All documents and vector index have been deleted.');
    }

    private function describeImage(string $absoluteImagePath, string $projectRoot): ?string
    {
        $scriptPath = $projectRoot.'/describe_image.py';

        if (! file_exists($scriptPath)) {
            return null;
        }

        $pythonExecutable = file_exists($projectRoot.'/.venv/bin/python')
            ? $projectRoot.'/.venv/bin/python'
            : 'python3';

        $env = array_merge($_ENV, [
            'VLM_URL'   => env('VLM_URL', 'http://192.168.100.67:1234'),
            'VLM_MODEL' => env('VLM_MODEL', 'google/gemma-4-e2b'),
        ]);

        $process = new Process([$pythonExecutable, $scriptPath, $absoluteImagePath], $projectRoot, $env);
        $process->setTimeout(180);
        $process->run();

        $output = trim($process->getOutput());
        if ($output === '') {
            return null;
        }

        $decoded = json_decode($output, true);
        if (is_array($decoded) && isset($decoded['description'])) {
            return $decoded['description'];
        }

        return null;
    }

    private function slugFilename(string $value): string
    {
        return Str::slug($value).'_'.Str::lower(Str::random(6));
    }

    private function runReindex(string $companyId): string
    {
        $projectRoot = dirname(base_path());
        $scriptPath = $projectRoot.'/reindex.py';

        if (! file_exists($scriptPath)) {
            throw new \RuntimeException('reindex.py script not found in project root.');
        }

        $pythonExecutable = file_exists($projectRoot.'/.venv/bin/python')
            ? $projectRoot.'/.venv/bin/python'
            : 'python3';

        $env = array_merge($_ENV, [
            'LMSTUDIO_URL'   => env('LMSTUDIO_URL', 'http://192.168.100.67:1234'),
            'LMSTUDIO_MODEL' => env('LMSTUDIO_MODEL', 'mistral-7b-instruct-v0.3'),
            'VLM_URL'        => env('VLM_URL', 'http://192.168.100.67:1234'),
            'VLM_MODEL'      => env('VLM_MODEL', 'google/gemma-4-e2b'),
        ]);

        $process = new Process([$pythonExecutable, $scriptPath, $companyId], $projectRoot, $env);
        $process->setTimeout(300);
        $process->mustRun();

        return trim($process->getOutput());
    }

    private function syncDataDirectoryRecords(string $companyId = null): void
    {
        $companyId = $companyId ?? (string) \Illuminate\Support\Facades\Auth::user()?->company_id;
        if (! $companyId) {
            return;
        }

        $projectRoot = dirname(base_path());
        $dataDir = $projectRoot.'/data/company_'.$companyId;

        if (! is_dir($dataDir)) {
            return;
        }

        $allowedExtensions = ['pdf', 'txt', 'md', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ignoredFilenames = ['faiss.index', 'faiss.meta.json'];

        foreach (File::allFiles($dataDir) as $file) {
            $filename = $file->getFilename();
            if (in_array($filename, $ignoredFilenames, true)) {
                continue;
            }

            // Skip comment files written by CommentController for RAG indexing
            if (str_starts_with($filename, 'comment_')) {
                continue;
            }

            $extension = Str::lower($file->getExtension());
            if (! in_array($extension, $allowedExtensions, true)) {
                continue;
            }

            $absolutePath = $file->getPathname();
            $relativePath = str_replace($projectRoot.'/', '', $absolutePath);

            Document::query()->firstOrCreate(
                ['file_path' => $relativePath],
                [
                    'title'      => Str::headline(pathinfo($filename, PATHINFO_FILENAME)),
                    'type'       => $extension,
                    'company_id' => $companyId,
                ]
            );
        }
    }
}
