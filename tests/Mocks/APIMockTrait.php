<?php
namespace Tests\Mocks;

use Illuminate\Http\Client\Response;

trait APIMockTrait
{
    private function createMockResponse(int $status, array $headers = [], array|object $body = []): Response
    {
        $guzzleResponse = new \GuzzleHttp\Psr7\Response($status, $headers, json_encode($body));

        return new Response($guzzleResponse);
    }
}