<?php

namespace App\Listeners;

use App\Events\ArticlePublished;
use App\Contracts\NotificationSenderInterface;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReaderNotification implements ShouldQueue
{
    use InteractsWithQueue;

  public string $queue = 'high'; 
    protected NotificationSenderInterface $sender;

    public function __construct(NotificationSenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function handle(ArticlePublished $event): void
    {
        $readers = User::where('role', 'reader')->get();
/**
 * @var \App\Models\User $reader
 */
        foreach ($readers as $reader) {
            $this->sender->send($reader, "New article available for reading: " . $event->article->title);
        }
    }
}