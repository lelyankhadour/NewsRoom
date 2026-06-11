<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Contracts\NotificationSenderInterface;
use App\Jobs\ProcessNotificationJob;
use App\Services\Notifications\DatabaseNotificationSender;
use App\Services\Notifications\EmailNotificationSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCommentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationSenderInterface $sender;

    public function __construct(NotificationSenderInterface $sender)
    {
        $this->sender = $sender;
    }

   

public function handle(CommentCreated $event): void
{
    $comment = $event->comment;
    $article = $comment->commentable;

 
    if ($article->user_id !== $comment->user_id) {
        ProcessNotificationJob::dispatch(
            $article->user, 
            "New comment: " . $article->title, 
            EmailNotificationSender::class
        );
    }

    $admins = \App\Models\User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        ProcessNotificationJob::dispatch(
            $admin, 
            "New comment on: {$article->title}", 
            DatabaseNotificationSender::class
        );
    }
}
}
