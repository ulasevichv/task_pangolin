<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api/v1/_root.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withBindings([
    ])
    ->withSingletons([
        App\ExternalServices\NewYorkTimes\BooksAPI::class => App\ExternalServices\NewYorkTimes\BooksAPI::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->stopIgnoring([
            HttpException::class,
            ValidationException::class,
        ]);

        $exceptions->report(function (HttpException|ValidationException $ex) {
            return false;
        });

        $exceptions->render(function (HttpException $ex, Request $request) {
            if ($request->is('api/*')) {
                return response()->json((object)[
                    'error' => $ex->getMessage(),
                ], $ex->getStatusCode());
            }

            return $this;
        });

        $exceptions->render(function (ValidationException $ex, Request $request) {
            if ($request->is('api/*')) {
                $allValidationErrors = $ex->errors();

                $allFieldErrors = [];
                foreach ($allValidationErrors as $fieldName => $fieldErrorText) {
                    $allFieldErrors[] = (object)[
                        'fieldName' => $fieldName,
                        'errors' => $fieldErrorText,
                    ];
                }

                $response = (object)[
                    'error' => $allFieldErrors[0]->errors[0],
                    'fieldErrors' => $allFieldErrors,
                ];

                return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return $this;
        });

        $exceptions->render(function (\Throwable $ex, Request $request) {
            if ($request->is('api/*')) {
                return response()->json((object)[
                    'error' => 'Something went wrong. Please, contact support for assistance',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this;
        });
    })
    ->create();