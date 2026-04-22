<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'comment_id'     => $this->comment->id,
            'document_id'    => $this->comment->document_id,
            'document_title' => $this->comment->document->title,
            'author_name'    => $this->comment->author->name,
            'excerpt'        => mb_substr($this->comment->content, 0, 120),
        ];
    }
}
