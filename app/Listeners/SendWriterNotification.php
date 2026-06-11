<?php

namespace App\Listeners;

use App\Events\ArticlePublished;
use App\Contracts\NotificationSenderInterface;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWriterNotification implements ShouldQueue
{
    use InteractsWithQueue;


    public string $queue = 'high';
    
    public int $tries = 3;
    public int $backoff = 15;

    protected NotificationSenderInterface $sender;

    /**
     * Create the event listener and inject the contractual interface.
     * The Service Container will contextually resolve this into EmailNotificationSender.
     */
    public function __construct(NotificationSenderInterface $sender)
    {
        $this->sender = $sender;
    }

//    before update this method we use it to search for all writers and send emails  but now we handle only the owner of article
public function handle(ArticlePublished $event): void
{
    \Log::info('DEBUG: Handle reached for article: ' . $event->article->id);
    
    try {
        // $writers = User::where('role', 'writer')->get();
        // \Log::info('DEBUG: Writers count: ' . $writers->count());

        $writer = $event->article->user;
        \Log::info('DEBUG: Sending to writer ID: ' . $writer->id);
        
        $this->sender->send($writer, "your article is published  : " . $event->article->title);
        
        \Log::info('DEBUG: Notification sent successfully.');

    } catch (Throwable $exception) {
        \Log::error('DEBUG ERROR: ' . $exception->getMessage());
        throw $exception; 
    }
}
}