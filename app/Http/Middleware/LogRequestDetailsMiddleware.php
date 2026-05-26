<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Log;


class LogRequestDetailsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
{
    $startTime = microtime(true);

    $response = $next($request);

    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000; 


    DB::table('api_request_logs')->insert([
        'user_id' => $request->user()?->id,
        'method' => $request->method(),
        'url' => $request->fullUrl(),
        'payload' => $request->except(['password', 'password_confirmation']) ? json_encode($request->all()) : null, // حماية كلمات المرور من التخزين
        'ip_address' => $request->ip(),
        'status_code' => $response->getStatusCode(),
        'execution_time_ms' => $executionTime,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
     $logData = [
            'user_id'     => auth()->id() ?? 'Guest',
            'ip_address'  => $request->ip(),
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'user_agent'  => $request->userAgent(),
        ];

        Log::info('API Request Logged:', $logData);
    return $response;
}
}