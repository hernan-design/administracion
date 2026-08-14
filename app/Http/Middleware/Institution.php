<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use App\Models\Param\Institucion;

class Institution
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
        if( !session('institution', false) ) {
            // Session prepare
            $institution = Institucion::getInstitution();
            session(['institution' => $institution]);
            session(['metatags' => $institution->metaTags()]);
            session(['color' => $institution->color()]);
        }

        return $next($request);
    }
}
