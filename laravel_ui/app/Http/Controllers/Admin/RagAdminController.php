<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RagAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.rag');
    }

    public function ask(Request $request): JsonResponse
    {
        @set_time_limit(300);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        $projectRoot = dirname(base_path());
        $scriptPath  = $projectRoot . '/rag_query.py';

        if (! file_exists($scriptPath)) {
            return response()->json(['error' => 'RAG bridge script not found.'], 500);
        }

        $python = file_exists($projectRoot . '/.venv/bin/python')
            ? $projectRoot . '/.venv/bin/python'
            : 'python3';

        $process = new Process([$python, $scriptPath, trim($validated['question'])], $projectRoot);
        $process->setTimeout(180);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            return response()->json([
                'error'   => 'Erreur du pipeline RAG.',
                'details' => trim($e->getProcess()->getErrorOutput()) ?: trim($e->getProcess()->getOutput()),
            ], 500);
        }

        $raw   = trim($process->getOutput());
        $lines = preg_split('/\R/', $raw) ?: [];
        $json  = trim(end($lines) ?: $raw);

        $data = json_decode($json, true);
        if (! is_array($data)) {
            return response()->json(['error' => 'Réponse invalide du pipeline.', 'raw' => $raw], 500);
        }

        return response()->json($data);
    }
}
