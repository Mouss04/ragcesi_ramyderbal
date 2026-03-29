<?php

namespace App\Http\Controllers;

use App\Models\RagHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RagController extends Controller
{
    public function ask(Request $request): JsonResponse
    {
        if (! $request->user()) {
            return response()->json([
                'error' => 'Authentication required.',
            ], 401);
        }

        @set_time_limit(300);

        $validated = $request->validate([
            'question'        => ['required', 'string', 'max:1000'],
            'chat_session_id' => ['nullable', 'string', 'max:36'],
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

        $question = trim($validated['question']);

        $process = new Process([
            $pythonExecutable,
            $scriptPath,
            $question,
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

        // Persist history for all authenticated users
        if ($request->user()) {
            $this->saveHistory(
                $request->user()->id,
                $question,
                $decoded,
                $validated['chat_session_id'] ?? null
            );
        }

        return response()->json($decoded);
    }

    private function saveHistory(int $userId, string $question, array $decoded, ?string $chatSessionId): void
    {
        RagHistory::query()->create([
            'user_id'         => $userId,
            'chat_session_id' => $chatSessionId,
            'question'        => $question,
            'answer'          => $decoded['answer'] ?? null,
            'sources'         => $decoded['sources'] ?? null,
        ]);
    }
}
