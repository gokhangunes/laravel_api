<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientToken
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
        $auth = $request->header('Authorization');


        if ($auth) {
            $token = trim(str_replace('Bearer', '', $auth));
        }

        if (empty($token)) {
            $token = $request->get('client-token');
        }

        if (empty($token)) {
            return response()->json([
                'message' => 'No Token!',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('client-token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid Token!',
            ], Response::HTTP_UNAUTHORIZED);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
