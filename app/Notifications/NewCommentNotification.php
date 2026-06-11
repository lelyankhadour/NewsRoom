<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public Comment $comment;

      public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

   
public function via($notifiable)
{
  
    $roleValue = $notifiable->role instanceof \App\Enums\UserRole 
                 ? $notifiable->role->value 
                 : $notifiable->role;

    if ($roleValue === \App\Enums\UserRole::WRITER->value) {
        return ['mail'];
    }

    return ['database'];
}


    public function toArray($notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'article_id' => $this->comment->commentable_id,
            'message'    => 'New comment: ' . $this->comment->content,
        ];
    }
}