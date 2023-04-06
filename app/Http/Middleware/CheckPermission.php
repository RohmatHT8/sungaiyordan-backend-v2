<?php

namespace App\Http\Middleware;

use App\Entities\PeriodPermission;
use App\Entities\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Exceptions\ValidatorException;

class CheckPermission extends Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $ability, ...$models)
    {
        try {
            $this->gate->authorize($ability, $this->getGateArguments($request, $models));
            $request->attributes->add([
                'ability' => $ability
            ]);
        }catch (AuthorizationException $e){
            return abort('403', $e);
        }
        return $next($request);
    }
}
