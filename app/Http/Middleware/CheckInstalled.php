<?php

namespace app\Http\Middleware;

use App\Attendize\Utils;
use App\Models\Account;
use Closure;
use Redirect;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         * Check if the 'installed' file has been created
         */
        if (!file_exists(base_path('installed')) && !Utils::isAttendize()) {
            return Redirect::to('install');
        }

        /*
         * Redirect user to signup page if there are no accounts
         */
        if (Account::count() === 0 && !$request->is('signup*')) {
            return redirect()->to('signup');
        }

        $response = $next($request);

        return $response;
    }
}
