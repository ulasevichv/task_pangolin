<?php
namespace Tests\Mocks;

use App\ExternalServices\NewYorkTimes\BooksAPI;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BooksAPIMock extends BooksAPI
{
    use APIMockTrait;

    protected function loadConfig(): object
    {
        return (object)[
            'apiKey' => '__MOCK__',
        ];
    }

    protected function sendRequest(PendingRequest $request, string $fullUrl): Response
    {
        return $this->createMockResponse(SymfonyResponse::HTTP_OK,
            [
                'Content-Type' => 'application/json',
            ],
            (object)File::json(base_path('tests/Fixtures/BestSellers_Search.json'))
        );
    }
}