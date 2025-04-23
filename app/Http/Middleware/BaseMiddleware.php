<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;

class BaseMiddleware
{
    public static function verifyRequestContentType(Request $request, string $requiredContentType): bool
    {
        return ($request->headers->has('Content-Type') && $request->header('Content-Type') === $requiredContentType);
    }
}