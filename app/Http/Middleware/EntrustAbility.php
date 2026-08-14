<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class EntrustAbility
{
    protected $auth;

    /**
     * Creates a new instance of the middleware.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param Closure $next
	 * @param $roles
	 * @param $permissions
	 * @param bool $validateAll
	 * @return mixed
	 */
	public function handle($request, Closure $next, $roles, $permissions, $module = null, $validateAll = false)
	{
		$module = !is_null($module) ? "/$module" : $request->route()->getCompiled()->getStaticPrefix();
        if ($this->auth->guest() || !$request->user()->ability(explode('|', $roles), explode('|', $permissions), array('validate_all' => $validateAll, 'module' => $module))) {
			abort(403);
		}

		return $next($request);
	}
}
