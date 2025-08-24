<?php

namespace App\Http\Middleware;

use App\Services\BusinessCentralAuthService;
use Closure;
use Illuminate\Http\Request;

class BCAuthMiddleware
{
    private BusinessCentralAuthService $authService;

    public function __construct(BusinessCentralAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $this->authService->getToken();

        if (!$token) {
            return response()->json(['error' => 'Unable to authenticate with Business Central'], 401);
        }

        return $next($request);
    }
}
