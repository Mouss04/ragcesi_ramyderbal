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

        $projectRoot = dirname(base_path());
        $targetDir = $projectRoot.'/data';

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $file = $data['file'];
        $extension = Str::lower($file->getClientOriginalExtension() ?: 'bin');
        $filename = now()->format('YmdHis').'_'.$this->slugFilename($data['title']).'.'.$extension;
        $file->move($targetDir, $filename);

        Document::query()->create([
            'title' => $data['title'],
            'file_path' => 'data/'.$filename,
        ]);

        $this->syncDataDirectoryRecords();

        try {
            $summary = $this->runReindex();
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

    private function slugFilename(string $value): string
    {
        return Str::slug($value).'_'.Str::lower(Str::random(6));
    }

    private function runReindex(): string
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
            'VLM_URL'   => env('VLM_URL', 'http://192.168.100.67:1234'),
            'VLM_MODEL' => env('VLM_MODEL', 'google/gemma-4-e2b'),
        ]);

        $process = new Process([$pythonExecutable, $scriptPath], $projectRoot, $env);
        $process->setTimeout(300);
        $process->mustRun();

        return trim($process->getOutput());
    }

    private function syncDataDirectoryRecords(): void
    {
        $projectRoot = dirname(base_path());
        $dataDir = $projectRoot.'/data';

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

            $extension = Str::lower($file->getExtension());
            if (! in_array($extension, $allowedExtensions, true)) {
                continue;
            }

            $absolutePath = $file->getPathname();
            $relativePath = str_replace($projectRoot.'/', '', $absolutePath);

            Document::query()->firstOrCreate(
                ['file_path' => $relativePath],
                ['title' => Str::headline(pathinfo($filename, PATHINFO_FILENAME)), 'company_id' => auth()->user()?->company_id]
            );
        }
    }
}
