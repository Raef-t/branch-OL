<?php

use App\Exceptions\DeletionRestrictedException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\Http\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'approved' => \App\Http\Middleware\EnsureUserIsApproved::class,
            'force-password-change' => \App\Http\Middleware\EnsurePasswordChangeIfForced::class,
            // 'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'permission' => \App\Http\Middleware\CustomPermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'door-device-auth' => \Modules\DoorSessions\Http\Middleware\EnsureDoorDeviceAuthorized::class,
            // 'is-system-enabled' => \App\Http\Middleware\CheckSystemStatus::class,
        ]);

        $middleware->group('api', [
            \App\Http\Middleware\CheckSystemStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (
            Illuminate\Validation\ValidationException $exception,
            $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => $exception->errors(),
                ], $exception->status);
            }
        });

        $exceptions->renderable(function (DecryptException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'رمز التوثيق غير صالح أو انتهت صلاحيته. يرجى تسجيل الدخول من جديد.',
                    'error_code' => 'INVALID_PAYLOAD',
                ], 401);
            }
        });

        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مصرح لك بالوصول، يرجى تسجيل الدخول.',
                ], 401);
            }
        });

        $exceptions->renderable(function (DeletionRestrictedException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'errors' => [
                        'resource' => $e->resource(),
                        'relations' => $e->relations(),
                    ],
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        });

        // $exceptions->renderable(function (Throwable $e, $request) {
        //     if ($request->is('api/*')) {
        //         $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        //         return response()->json([
        //             'status'   => false,
        //             'message'   => $e->getMessage(),
        //             'exception' => get_class($e),
        //             'file'      => $e->getFile(),
        //             'line'      => $e->getLine(),
        //             'trace'     => config('app.debug') ? collect($e->getTrace())->take(5) : [],
        //         ], $status);
        //     }
        // });
    })
    ->create();

