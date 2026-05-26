<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponse; 
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    use ApiResponse; 

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles 
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
   
        if (!auth()->check()) {
            return $this->errorResponse(
                'Unauthenticated. Please log in to proceed.', 
                Response::HTTP_UNAUTHORIZED
            ); 
        }

   
        if (in_array(auth()->user()->role->value, $roles)) {
            return $next($request); 
        }

        return $this->errorResponse(
            'Unauthorized. You do not have the required permissions for this action.', 
            Response::HTTP_FORBIDDEN
        );
    }
}