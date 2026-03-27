<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DocumentController extends Controller
{
    public function index(): View
    {
        return view('admin.documents.index', [
            'documents' => Document::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $projectRoot = dirname(base_path());
        $targetDir = $projectRoot.'/data/uploads';

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $file = $data['file'];
        $filename = now()->format('YmdHis').'_'.$this->slugFilename($data['title']).'.pdf';
        $file->move($targetDir, $filename);

        Document::query()->create([
            'title' => $data['title'],
            'file_path' => 'data/uploads/'.$filename,
        ]);

        return back()->with('status', 'Document uploaded. You can now process documents.');
    }

    public function process(): RedirectResponse
    {
        @set_time_limit(300);

        $projectRoot = dirname(base_path());
        $scriptPath = $projectRoot.'/reindex.py';

        if (! file_exists($scriptPath)) {
            return back()->withErrors(['process' => 'reindex.py script not found in project root.']);
        }

        $pythonExecutable = file_exists($projectRoot.'/.venv/bin/python')
            ? $projectRoot.'/.venv/bin/python'
            : 'python3';

        $process = new Process([$pythonExecutable, $scriptPath], $projectRoot);
        $process->setTimeout(240);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            $errorOutput = trim($exception->getProcess()->getErrorOutput());
            $fallbackOutput = trim($exception->getProcess()->getOutput());

            return back()->withErrors([
                'process' => 'Document processing failed: '.($errorOutput ?: $fallbackOutput),
            ]);
        }

        return back()->with('status', 'Documents indexed successfully.');
    }

    private function slugFilename(string $value): string
    {
        return Str::slug($value).'_'.Str::lower(Str::random(6));
    }
}
