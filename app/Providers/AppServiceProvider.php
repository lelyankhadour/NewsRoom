<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;


use App\Contracts\{ArticleRepositoryInterface, NotificationSenderInterface, ReportRepositoryInterface, UserRepositoryInterface};
use App\Models\{Article, User};
use App\Repositories\Eloquent\{CachingArticleRepository, CachingReportRepository, EloquentArticleRepository, EloquentReportRepository, EloquentUserRepository};
use App\Services\Notifications\{DatabaseNotificationSender, EmailNotificationSender};
use App\Services\Reports\{ReportService, WeeklyArticlesReport, MonthlyAuthorsActivityReport};
use App\Listeners\{ SendAdminNotification, SendWriterNotification, SendWelcomeEmail};
use App\Observers\ArticleObserver;
use Log;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        \Log::info("ServiceProvider is loading...");
        //  Repositories Pattern & Singletone
        $this->app->singleton(ArticleRepositoryInterface::class, fn() => 
            new CachingArticleRepository(new EloquentArticleRepository(new Article()))
        );
// using Decorated pattern in  cachingRepository 
        $this->app->singleton(ReportRepositoryInterface::class, fn() => 
            new CachingReportRepository(new EloquentReportRepository(new Article(), new User()))
        );

        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);

        //  Strategy Pattern 
        $this->app->bind(ReportService::class, fn($app) => 
            new ReportService([
                $app->make(WeeklyArticlesReport::class),
                $app->make(MonthlyAuthorsActivityReport::class),
            ])
        );

        //  Contextual Binding 
        $this->app->when(SendAdminNotification::class)
                  ->needs(NotificationSenderInterface::class)
                  ->give(function () {
              Log::info("Binding Triggered: Injecting DatabaseNotificationSender");
              return new DatabaseNotificationSender();
          });
                //   ->give(DatabaseNotificationSender::class);

        $this->app->when(SendWriterNotification::class)
                  ->needs(NotificationSenderInterface::class)
                    ->give(EmailNotificationSender::class);}
   
    public function boot(): void

    {
        // Observers & Rate Limiting
        Article::observe(ArticleObserver::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}