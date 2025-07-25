<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\IsOnDevelopment;
use App\Http\Middleware\HasAccessToFinanceModule;
use App\Http\Middleware\OnlyForAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__ . '/../routes/api.php',
        // commands: __DIR__ . '/../routes/console.php',
        // health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('auth.development', [IsOnDevelopment::class]);
        $middleware->appendToGroup('auth.module.finance', [HasAccessToFinanceModule::class]);
        $middleware->appendToGroup('auth.admin', [OnlyForAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Exception $e) {
            if (method_exists($e, 'getStatusCode')) {
                if ($e->getStatusCode() == 419) {
                    return redirect()->route('login')->with([
                        'pesan' => 'Sesi keamanan browser Anda telah berakhir. Mohon login kembali.',
                        'level-alert' => 'alert-warning'
                    ]);
                }
            }
        });
    })->create();
