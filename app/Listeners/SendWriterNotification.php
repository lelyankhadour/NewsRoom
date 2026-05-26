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

    /**
     * Handle the published article event for writers context.
     */
    public function handle(ArticlePublished $event): void
    {
        try {
            // Retrieve all writers to notify them about the new publication
            $writers = User::where('role', 'writer')->get();

            if ($writers->isEmpty()) {
                Log::info('No writers found to notify for this article.', [
                    'article_id' => $event->article->id
                ]);
                return;
            }

 /**
                 * @var \App\Models\User  $writer
                 */
            foreach ($writers as $writer) {
                // Execute contextual abstraction layers to send email alerts securely
                $this->sender->send($writer, "New article published by team: " . $event->article->title);
            }

            Log::info('SendWriterNotification listener executed successfully.', [
                'article_id' => $event->article->id,
                'recipients_count' => $writers->count()
            ]);

        } catch (Throwable $exception) {
            Log::error('Error occurred inside SendWriterNotification listener execution.', [
                'article_id' => $event->article->id,
                'error_message' => $exception->getMessage()
            ]);

            // Release the job securely back into background system structures
            $this->release($this->backoff);
        }
    }
}