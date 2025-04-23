<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckJson extends BaseMiddleware
{
    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requiredContentType = 'application/json';

        if (!static::verifyRequestContentType($request, $requiredContentType)) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid content type. Should be: ' . $requiredContentType);
        }

        if (!json_validate($request->getContent())) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid JSON provided');
        }

        return $next($request);
    }
}