<?php

namespace App\Listeners;

use App\Events\ArticlePublished;
use App\Contracts\NotificationSenderInterface;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendAdminNotification implements ShouldQueue
{
    use InteractsWithQueue;

    // High priority execution queue channel mapping
    public string $queue = 'high';
    
    public int $tries = 3;

    public int $backoff = 15;

    protected NotificationSenderInterface $sender;

    /**
     * Create the event listener and inject the contractual interface.
     * The Service Container will contextually resolve this into DatabaseNotificationSender.
     */
    public function __construct(NotificationSenderInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Handle the published article event for administrators context.
     */
    public function handle(ArticlePublished $event): void
    {
        try {
            // Retrieve all administrators to store system dashboard notifications
            $admins = User::where('role', 'admin')->get();

            if ($admins->isEmpty()) {
                Log::warning('No administrators found in the platform database layers.');
                return;
            }
 /**
                 * @var \App\Models\User $admin
                 */
            foreach ($admins as $admin) {
                // Execute contextual abstraction layers to trigger real-time database tracking
                $this->sender->send($admin, "System Alert: Verification required for article: " . $event->article->title);
            }

            Log::info('SendAdminNotification listener completed processing routing loops.', [
                'article_id' => $event->article->id,
                'admins_notified' => $admins->count()
            ]);

        } catch (Throwable $exception) {
            Log::error('Fatal error triggered inside SendAdminNotification handler routines.', [
                'article_id' => $event->article->id,
                'exception' => $exception->getMessage()
            ]);

            // Release back to queue architecture tracking models
            $this->release($this->backoff);
        }
    }
}