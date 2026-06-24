<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => config('cors.allow_origin', '*'),
            'Access-Control-Allow-Methods'     => config('cors.allow_methods', 'GET,POST,PATCH,PUT,DELETE,OPTIONS'),
            'Access-Control-Allow-Credentials' => config('cors.allow_credentials', true),
            'Access-Control-Allow-Headers'     => config('cors.allow_headers', 'Content-Type,Authorization'),
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->json('', 200, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }

}
