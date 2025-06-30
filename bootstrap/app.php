<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        // تمت إزالة: middleware: [...], من هنا
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // هنا يجب تعريف الـ middleware المخصص الخاص بك
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'team.role' => \App\Http\Middleware\TeamRoleMiddleware::class,
        ]);

        // إذا كان لديك أي middleware عام، يمكنك تعريفه هنا أيضًا، على سبيل المثال:
        // $middleware->web(App\Http\Middleware\EncryptCookies::class);
        // $middleware->web(App\Http\Middleware\PreventRequestsDuringMaintenance::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();