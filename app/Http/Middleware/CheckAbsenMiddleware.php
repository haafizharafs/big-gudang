<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAbsenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $absen = \App\Models\Absen::where('user_id', auth()->user()->id)
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        if ($absen) {
            if ($absen->isAbsen()) {
                return redirect(url('absen/now'));
            }
            return $next($request);
        }

        if (date('H:i') >= \App\Models\Absen::$settedTime[0]  && date('H:i') < \App\Models\Absen::$alpa) {
            return redirect(url('absen/now'));
        }

        return $next($request);
    }
}
