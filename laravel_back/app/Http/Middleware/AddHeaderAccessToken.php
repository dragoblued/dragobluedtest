<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddHeaderAccessToken
{
   /**
    * Handle an incoming request.
    *
    * @param Request $request
    * @param Closure $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
       if ($request->has('access_token')) {
          $token = $request->get('access_token');
          $request->headers->set('Authorization', 'Bearer ' . $token);
       }
       return $next($request);
    }
}
