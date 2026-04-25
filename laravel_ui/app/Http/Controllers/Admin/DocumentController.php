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

    public function store(Request $request): \Illuminate\Http\JsonResponse
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
            } catch (\RuntimeException $e) {
                if ($e->getCode() === 2) {
                    // Blurry image: remove the uploaded file and reject the request.
                    @unlink($targetDir.'/'.$filename);
                    return response()->json([
                        'message' => $e->getMessage(),
                        'errors'  => ['file' => [$e->getMessage()]],
                    ], 422);
                }
                $description = null;
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
            $this->runReindex($companyId);
        } catch (\Throwable) {
            // Index build failure does not block the upload — it will be rebuilt on next query.
        }

        return response()->json(['ok' => true]);
    }

    public function reindexStream(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $companyId = (string) $request->user()->company_id;
        $projectRoot = dirname(base_path());
        $scriptPath = $projectRoot.'/reindex.py';

        $pythonExecutable = file_exists($projectRoot.'/.venv/bin/python')
            ? $projectRoot.'/.venv/bin/python'
            : 'python3';

        $env = array_merge($_ENV, [
            'LMSTUDIO_URL'   => env('LMSTUDIO_URL', 'http://192.168.100.67:1234'),
            'LMSTUDIO_MODEL' => env('LMSTUDIO_MODEL', 'mistral-7b-instruct-v0.3'),
            'VLM_URL'        => env('VLM_URL', 'http://192.168.100.67:1234'),
            'VLM_MODEL'      => env('VLM_MODEL', 'google/gemma-4-e2b'),
        ]);

        return response()->stream(function () use ($pythonExecutable, $scriptPath, $companyId, $env, $projectRoot) {
            // Disable all output buffering so SSE events are sent immediately.
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(true);

            $process = new Process([$pythonExecutable, $scriptPath, $companyId], $projectRoot, $env);
            $process->setTimeout(300);
            $process->start();

            while ($process->isRunning()) {
                $out = $process->getIncrementalOutput();
                if ($out !== '') {
                    foreach (explode("\n", $out) as $raw) {
                        $line = trim($raw);
                        if ($line === '') {
                            continue;
                        }
                        if (preg_match('/^PROGRESS:(\d+):(\S+)$/', $line, $m)) {
                            echo 'data: '.json_encode(['pct' => (int) $m[1], 'label' => $m[2]])."\n\n";
                            flush();
                        }
                    }
                }
                usleep(80000); // 80 ms
            }

            if ($process->getExitCode() !== 0) {
                $err = trim($process->getErrorOutput() ?: $process->getOutput());
                echo 'data: '.json_encode(['error' => $err])."\n\n";
            } else {
                $output = trim($process->getOutput());
                echo 'data: '.json_encode(['done' => true, 'status' => $output])."\n\n";
            }
            flush();
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
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
            'VLM_URL'        => env('VLM_URL', 'http://192.168.100.67:1234'),
            'VLM_MODEL'      => env('VLM_MODEL', 'google/gemma-4-e2b'),
            'BLUR_THRESHOLD' => env('BLUR_THRESHOLD', '100.0'),
        ]);

        $process = new Process([$pythonExecutable, $scriptPath, $absoluteImagePath], $projectRoot, $env);
        $process->setTimeout(180);
        $process->run();

        $output = trim($process->getOutput());

        // Exit code 2 means the blur check failed — image is too blurry for OCR.
        if ($process->getExitCode() === 2) {
            $decoded = json_decode($output, true);
            $msg = is_array($decoded) && isset($decoded['error'])
                ? $decoded['error']
                : "L'image téléversée est trop floue pour être traitée. Veuillez téléverser une version plus nette.";
            throw new \RuntimeException($msg, 2);
        }

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
