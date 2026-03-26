<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RagController extends Controller
{
    public function ask(Request $request): JsonResponse
    {
        @set_time_limit(300);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $projectRoot = dirname(base_path());
        $scriptPath = $projectRoot.'/rag_query.py';

        if (! file_exists($scriptPath)) {
            return response()->json([
                'error' => 'RAG bridge script not found: rag_query.py',
            ], 500);
        }

        $pythonExecutable = file_exists($projectRoot.'/.venv/bin/python')
            ? $projectRoot.'/.venv/bin/python'
            : 'python3';

        $process = new Process([
            $pythonExecutable,
            $scriptPath,
            $validated['question'],
        ], $projectRoot);

        $process->setTimeout(180);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            return response()->json([
                'error' => 'RAG pipeline execution failed.',
                'details' => trim($exception->getProcess()->getErrorOutput()) ?: trim($exception->getProcess()->getOutput()),
            ], 500);
        }

        $rawOutput = trim($process->getOutput());
        $outputLines = preg_split('/\R/', $rawOutput) ?: [];
        $jsonCandidate = trim(end($outputLines) ?: $rawOutput);
        $decoded = json_decode($jsonCandidate, true);

        if (! is_array($decoded)) {
            return response()->json([
                'error' => 'Invalid response from RAG pipeline.',
                'details' => $rawOutput,
            ], 500);
        }

        return response()->json($decoded);
    }
}
