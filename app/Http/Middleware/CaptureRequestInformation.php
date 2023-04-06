<?php

namespace App\Http\Middleware;

use App\Entities\AccessLog;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CaptureRequestInformation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $requestUrl = $request->route()->uri;
        $duration = round((microtime(true) - LARAVEL_START)*1000); // in 

        AccessLog::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'request_url' => $requestUrl,
            'duration' => $duration
        ]);
    }
}
