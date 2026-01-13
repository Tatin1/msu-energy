<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIoTToken
{
    /**
     * Validate the incoming IoT token header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.iot.token');
        $provided = (string) $request->header('X-IOT-TOKEN', '');

        $missingConfig = $expected === '';
        $invalidHeader = $provided === '' || ! hash_equals($expected, $provided);

        if ($missingConfig || $invalidHeader) {
            abort(401, 'Invalid IoT token');
        }

        return $next($request);
    }
}
