<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Role\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Admin
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
        if (Auth::check()) {
            \Illuminate\Support\Facades\App::setLocale(userLocal());
            if (@Auth::user()) {
                if (isModuleActive('Break')) {
                    $isUserBreak = DB::table('user_breaks')
                        ->where('user_id', Auth::user()->id)
                        ->whereNull('end_time')
                        ->first();
                    if ($isUserBreak && !request()->routeIs('break.edit') && !request()->routeIs('break.update')) {
                        // return $next($request);
                        return redirect()->route('break.edit', $isUserBreak->id);
                    } else {
                        return $next($request);
                    }
                } else {
                    return $next($request);
                }
                return $next($request);
            } else {
                abort('401');
            }
        } else {
            return redirect('/');
        }
    }
}
