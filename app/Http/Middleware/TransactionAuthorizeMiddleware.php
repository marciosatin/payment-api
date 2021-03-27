<?php

namespace App\Http\Middleware;

use App\Services\AuthorizingExternalService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionAuthorizeMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!AuthorizingExternalService::isAuthorized()) {
            return response()->json([
                'transaction' => ['Unauthorized transaction']
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

}