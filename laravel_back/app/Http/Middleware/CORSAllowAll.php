<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CORSAllowAll
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Access-Control-Allow-Credentials, X-Requested-With, X-Socket-Id, Content-Type, Accept, Origin, Authorization, X-Authorization, Same-site, Secure, Cache-control, X-Playback-Session-Id, Content-Length, Accept-Language, Date, Expires, Server, Host, Referer, Connection, Accept-Encoding',
            'Access-Control-Expose-Headers' => 'Authorization, Access-Control-Allow-Credentials, X-Authorization',
            'Access-Control-Request-Headers' => 'X-Authorization'
        ];

        if($request->getMethod() === 'OPTIONS') {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return \response('', 200, $headers);
        }

        $response = $next($request);
        foreach($headers as $key => $value)
            $response->headers->set($key, $value);
        return $response;
    }
}
