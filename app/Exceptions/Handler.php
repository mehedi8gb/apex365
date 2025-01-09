<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        // Check for ValidationException
        if ($e instanceof ValidationException) {
            $errors = $e->errors();
            $firstErrorMessage = collect($errors)->flatten()->first(); // Get the first error message

            return response()->json([
                'success' => false,
                'message' => $firstErrorMessage,
            ], 422);
        }

        // Fallback to parent render
        return parent::render($request, $e);
    }
}

