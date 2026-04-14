<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التأكد من أن المستخدم مسجل دخوله (يجب أن يكون موجودًا)
        if ($request->user() && !$request->user()->is_approved) {
            return response()->json([
                'status' => false,
                'message' => 'حسابك غير مفعل بعد. يرجى الانتظار حتى تتم الموافقة .',
                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}
