<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Document;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\Process\Process;

class CommentController extends Controller
{
    /**
     * Show the comment history & moderation page (admin/supervisor only).
     */
    public function index(Request $request): View
    {
        $user     = $request->user();
        $status   = $request->query('status');

        $query = Comment::query()
            ->with(['document', 'author', 'moderator'])
            ->whereHas('document', fn ($q) => $q->where('company_id', $user->company_id))
            ->latest();

        if ($status && in_array($status, ['PENDING', 'APPROVED', 'REJECTED'], true)) {
            $query->where('status', $status);
        }

        $comments = $query->paginate(20)->withQueryString();

        $counts = Comment::query()
            ->whereHas('document', fn ($q) => $q->where('company_id', $user->company_id))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.comments.index', compact('comments', 'counts', 'status'));
    }

    /**
     * Store a new comment (all authenticated roles).
     */
    public function store(Request $request, int $documentId): JsonResponse
    {
        @set_time_limit(0);

        $data = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $user = $request->user();

        /** @var \App\Models\Document $document */
        $document = Document::query()
            ->where('id', $documentId)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $isModerator = in_array($user->role, ['admin', 'supervisor'], true);
        $status      = $isModerator ? 'APPROVED' : 'PENDING';

        $comment = Comment::query()->create([
            'document_id'          => $document->id,
            'author_user_id'       => $user->id,
            'content'              => $data['content'],
            'status'               => $status,
            'moderated_by_user_id' => $isModerator ? $user->id : null,
            'moderated_at'         => $isModerator ? now() : null,
        ]);

        $comment->load(['document', 'author']);

        if ($status === 'APPROVED') {
            $this->indexComment($comment);
        }

        // Notify all admins and supervisors in the same company when a new comment is pending
        if ($status === 'PENDING') {
            $moderators = User::where('company_id', $user->company_id)
                ->whereIn('role', ['admin', 'supervisor'])
                ->get();

            foreach ($moderators as $moderator) {
                $moderator->notify(new NewCommentNotification($comment));
            }
        }

        return response()->json([
            'success' => true,
            'status'  => $status,
            'message' => $isModerator
                ? __('Comment published and sent to RAG indexing.')
                : __('Comment submitted and pending moderation.'),
        ]);
    }

    /**
     * Approve a pending comment (admin/supervisor only).
     */
    public function approve(Request $request, Comment $comment): RedirectResponse|JsonResponse
    {
        @set_time_limit(0);
        $user = $request->user();

        $comment->load('document');
        if ((string) $comment->document->company_id !== (string) $user->company_id) {
            abort(403);
        }

        if ($comment->status !== 'PENDING') {
            if ($request->wantsJson()) {
                return response()->json(['error' => __('Only pending comments can be approved.')], 422);
            }
            return back()->withErrors(['comment' => __('Only pending comments can be approved.')]);
        }

        $comment->update([
            'status'               => 'APPROVED',
            'moderated_by_user_id' => $user->id,
            'moderated_at'         => now(),
        ]);

        $this->indexComment($comment);

        if ($request->wantsJson()) {
            $dismissedIds = $this->dismissNotificationsForComment($user, $comment->id);
            return response()->json([
                'message'       => __('Comment approved and indexed in the RAG pipeline.'),
                'dismissed_ids' => $dismissedIds,
            ]);
        }
        return back()->with('status', __('Comment approved and indexed in the RAG pipeline.'));
    }

    /**
     * Reject a pending comment (admin/supervisor only).
     */
    public function reject(Request $request, Comment $comment): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $comment->load('document');
        if ((string) $comment->document->company_id !== (string) $user->company_id) {
            abort(403);
        }

        if ($comment->status !== 'PENDING') {
            if ($request->wantsJson()) {
                return response()->json(['error' => __('Only pending comments can be rejected.')], 422);
            }
            return back()->withErrors(['comment' => __('Only pending comments can be rejected.')]);
        }

        $comment->update([
            'status'               => 'REJECTED',
            'moderated_by_user_id' => $user->id,
            'moderated_at'         => now(),
        ]);

        if ($request->wantsJson()) {
            $dismissedIds = $this->dismissNotificationsForComment($user, $comment->id);
            return response()->json([
                'message'       => __('Comment rejected.'),
                'dismissed_ids' => $dismissedIds,
            ]);
        }
        return back()->with('status', __('Comment rejected.'));
    }

    /**
     * Mark all unread notifications for a given comment as read and return their IDs.
     */
    private function dismissNotificationsForComment($user, int $commentId): array
    {
        $notifications = $user->unreadNotifications
            ->filter(fn ($n) => ($n->data['comment_id'] ?? null) == $commentId);

        $ids = $notifications->pluck('id')->all();
        $notifications->each->markAsRead();

        return $ids;
    }

    /**
     * Write the approved comment as a text file and trigger FAISS reindex.
     */
    private function indexComment(Comment $comment): void
    {
        $companyId   = (string) $comment->document->company_id;
        $projectRoot = dirname(base_path());
        $companyDir  = $projectRoot . '/data/company_' . $companyId;

        if (! is_dir($companyDir)) {
            mkdir($companyDir, 0755, true);
        }

        $filename = 'comment_' . $comment->id . '_' . now()->format('YmdHis') . '.txt';
        $filepath = $companyDir . '/' . $filename;

        $header = '[Comment on: ' . $comment->document->title . ']' . PHP_EOL;
        file_put_contents($filepath, $header . $comment->content);

        $scriptPath = $projectRoot . '/reindex.py';
        if (! file_exists($scriptPath)) {
            return;
        }

        $pythonExecutable = file_exists($projectRoot . '/.venv/bin/python')
            ? $projectRoot . '/.venv/bin/python'
            : 'python3';

        $env = array_merge($_ENV, [
            'LMSTUDIO_URL'   => env('LMSTUDIO_URL', 'http://192.168.100.67:1234'),
            'LMSTUDIO_MODEL' => env('LMSTUDIO_MODEL', 'mistral-7b-instruct-v0.3'),
            'VLM_URL'        => env('VLM_URL', 'http://192.168.100.67:1234'),
            'VLM_MODEL'      => env('VLM_MODEL', 'google/gemma-4-e2b'),
        ]);

        try {
            $process = new Process([$pythonExecutable, $scriptPath, '--text-only', $companyId], $projectRoot, $env);
            $process->setTimeout(null);
            $process->run(); // wait for full completion — time limit already removed above
        } catch (\Throwable) {
            // Silently absorb — comment is saved regardless of index failure.
        }
    }
}
