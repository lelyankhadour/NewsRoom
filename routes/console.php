<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('articles:archive')->monthlyOn(1, '08:00');
// we can use parametar to get a weekly report
// Schedule::command('articles:report monthly_authors_activity_report')
//     ->monthlyOn(5, '08:00');
Schedule::command('articles:report ')
    ->monthlyOn(5, '08:00');

// اضافة الجدولة التلقائية قبل الرفع لتجنب الخطأ
