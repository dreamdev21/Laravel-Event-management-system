<?php namespace app\Http\Middleware;

use Closure;
use Redirect;
use Request;
use App\Models\OrderStatus;
use App\Attendize\Utils;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!file_exists(base_path('installed')) && !Utils::isAttendize()) {
            return Redirect::to('install');
        }

        $response = $next($request);

        return $response;
    }
}