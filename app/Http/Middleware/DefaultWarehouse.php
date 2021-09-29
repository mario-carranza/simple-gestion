<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Commune;

class DefaultWarehouse
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
        // Setear Puerto Montt como communa por default
        /* if (!session()->has('commune_id') || session('commune_id') != 288) {
            $commune = Commune::find(228);
            $request->session()->put('commune_id', $commune->id);
            $request->session()->put('commune_name', $commune->name);
        } */

        return $next($request);
    }
}
