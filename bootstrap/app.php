<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    //
  })
  ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->renderable(function (NotFoundHttpException $e, $request) {
      return response()->json([
        'error' => 'Not Found',
        'message' => 'The requested resource was not found'
      ], 404);
    });

    $exceptions->renderable(function (HttpException $e, $request) {
      if ($e->getStatusCode() == 500) {
        return response()->json([
          'error' => 'Internal Server Error',
          'message' => 'An unexpected error occurred'
        ], 500);
      }
    });
  })
  ->create();
