<?php

namespace App\Jobs;

use App\Enums\UserRole;
use App\Mail\WeeklyReportMail;
use App\Models\User;
use App\Services\Reports\ReportService; 
use App\Services\Notifications\DatabaseNotificationSender; 
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateWeeklyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct()
    {
        $this->onQueue('reports'); 
    }


    public function handle(ReportService $reportService): void
{

    $reportData = $reportService->generateReport('weekly_articles_report');

    $admins = User::where('role', 'admin')->get();

    foreach ($admins as $admin) {
    
        $sender = app(DatabaseNotificationSender::class);
        /**
         * @var \App\Models\User $admin
         */
        $sender->send($admin, 
"The weekly report is complete; you can view the details in your email or the dashboard." );
         Log::info(' administrators found to receive the weekly report.');
  
        \Illuminate\Support\Facades\Mail::to($admin->email)->send(new WeeklyReportMail($reportData));
    }
}
    public function failed(Throwable $exception): void
    {
        Log::error('Weekly Report Generation Job completely failed.', [
            'exception_message' => $exception->getMessage(),
        ]);
    }
}