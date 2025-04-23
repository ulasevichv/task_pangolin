<?php declare(strict_types=1);
namespace Tests\Feature\API;

use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class BaseAPITestCase extends TestCase
{
    protected const API_ROOT_URL = '/api/v1';

    protected static string $ENDPOINT_NAME = '';

    protected function getEndpointUrl(): string
    {
        return self::API_ROOT_URL . '/' . static::$ENDPOINT_NAME;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->flushHeaders();
        $this->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    protected function _assertErrorStatusAndMessageStartsWith(TestResponse $response, int $httpStatusCode, ?string $messageStartsWith = null): void
    {
        $this->assertTrue($response->getStatusCode() === $httpStatusCode);

        $content = $response->getContent();

        $this->assertTrue(json_validate($content));

        $contentObj = json_decode($content);

        $this->assertObjectHasProperty('error', $contentObj);

        $this->assertStringStartsWith($messageStartsWith, $contentObj->error);
    }

    protected function _assertErrorStatusAndMessageContains(TestResponse $response, int $httpStatusCode, ?string $messageContains = null): void
    {
        $this->assertTrue($response->getStatusCode() === $httpStatusCode);

        $content = $response->getContent();

        $this->assertTrue(json_validate($content));

        $contentObj = json_decode($content);

        $this->assertObjectHasProperty('error', $contentObj);

        $this->assertStringContainsString($messageContains, $contentObj->error);
    }

    protected function generateRandomString(int $length): string
    {
        if ($length <= 0) {
            return '';
        }

        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $numChars = strlen($chars);

        $feed = [];
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, $numChars - 1);
            $feed[] = substr($chars, $index, 1);
        }

        return implode('', $feed);
    }

    protected function logResponse(TestResponse $response): void
    {
        Log::info(
            PHP_EOL . 'STATUS:  ' . $response->getStatusCode()
            . PHP_EOL . 'CONTENT: ' . $response->getContent()
            . PHP_EOL);
    }
}