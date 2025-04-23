<?php
namespace App\ExternalServices\NewYorkTimes;

use App\Helpers\UrlHelper;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BooksAPI
{
    private const API_KEY_SETTING_NAME = 'NEW_YORK_TIMES_SERVICE_API_KEY';

    protected const SERVICE_ROOT_URL = 'https://api.nytimes.com/svc/books/v3';

    private static object $config;

    private static function renderServiceErrorMessage(Response $response): string
    {
        $responseObj = $response->object();

        $message = $responseObj->fault->faultstring ?? 'Undefined';
        $errorCode = $responseObj->fault->detail->errorcode ?? 'Undefined';

        return sprintf("Invalid response from NewYorkTimes BooksAPI service: %s (%s)\nFull response:\n%s\n", $message, $errorCode, $response);
    }

    protected function loadConfig(): object
    {
        $config = (object)[
            'apiKey' => env(static::API_KEY_SETTING_NAME),
        ];

        if (empty($config->apiKey)) {
            throw new \Exception(sprintf("Missing required configuration parameter: %s", static::API_KEY_SETTING_NAME));
        }

        return $config;
    }

    protected function getConfig(): object
    {
        if (!isset(static::$config)) {
            static::$config = static::loadConfig();
        }

        return static::$config;
    }

    protected function sendRequest(PendingRequest $request, string $fullUrl): Response
    {
        return $request->get($fullUrl);
    }

    public function search(string $author = '', string $title = '', array $isbnList = [], int $offset = 0): object
    {
        $url = static::SERVICE_ROOT_URL . '/lists/best-sellers/history.json';

        $queryParameters = [
            'api-key' => $this->getConfig()->apiKey,
        ];

        if (!empty($author)) {
            $queryParameters['author'] = $author;
        }

        if (!empty($title)) {
            $queryParameters['title'] = $title;
        }

        if (!empty($isbnList)) {
            $queryParameters['isbn'] = function () use ($isbnList) {
                return implode(';', $isbnList);
            };
        }

        if (!empty($offset)) {
            $queryParameters['offset'] = $offset;
        }

        $request = Http::createPendingRequest()
            ->acceptJson();

        $fullUrl = $url . UrlHelper::getUrlQueryString($queryParameters);

        $response = $this->sendRequest($request, $fullUrl);

        if (!$response->successful()) {
            throw new \Exception(static::renderServiceErrorMessage($response));
        }

        return $response->object();
    }
}