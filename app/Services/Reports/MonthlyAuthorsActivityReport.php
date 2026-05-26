<?php

namespace App\Services\Reports;

use App\Contracts\ReportGeneratorInterface;
use App\Models\User;
use Carbon\Carbon;

class MonthlyAuthorsActivityReport implements ReportGeneratorInterface
{
    public function generate(): array
{
    return User::where('role', 'writer')
        ->withCount(['articles' => function ($query) {
            $query->where('status', 'published')
                  ->where('created_at', '>=', Carbon::now()->startOfMonth());
        }])
        ->get()
        // in case two user have the same name
        ->mapWithKeys(function ($user) {
            return [$user->name . ' (ID:' . $user->id . ')' => $user->articles_count];
        })
        ->toArray();
}
    
    public function getReportName(): string
    {
        return 'monthly_authors_activity_report';
    }
}