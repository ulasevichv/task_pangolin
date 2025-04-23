<?php declare(strict_types=1);
namespace Tests\Feature\API\BestSellers;

use App\ExternalServices\NewYorkTimes\BooksAPI;
use App\Models\API\BestSellers\BestSeller;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\API\BaseAPITestCase;
use Tests\Mocks\BooksAPIMock;

final class Search_Test extends BaseAPITestCase
{
    protected static string $ENDPOINT_NAME = 'best-sellers/search';

    protected function setUp(): void
    {
        parent::setUp();

        app()->singleton(BooksAPI::class, BooksAPIMock::class);
    }

    #[Test]
    public function requestTypes(): void
    {
        $response = $this->call('OPTIONS', self::getEndpointUrl());
        $response->assertStatus(Response::HTTP_OK);

        $response = $this->call('GET', self::getEndpointUrl());
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);

        $response = $this->call('HEAD', self::getEndpointUrl());
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);

        $response = $this->call('PUT', self::getEndpointUrl());
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);

        $response = $this->call('PATCH', self::getEndpointUrl());
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);

        $response = $this->call('DELETE', self::getEndpointUrl());
        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Test]
    public function headers(): void
    {
        $response = $this->post(self::getEndpointUrl(), []);
        $this->_assertErrorStatusAndMessageStartsWith($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid JSON');
    }

    #[Test]
    public function authorField(): void
    {
        $response = $this->postJson(self::getEndpointUrl(), ['author' => 111]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must be a string');

        $tooLongString = $this->generateRandomString(BestSeller::AUTHOR_FIELD_MAX_LENGTH + 1);

        $response = $this->postJson(self::getEndpointUrl(), ['author' => $tooLongString]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must not be greater than');
    }

    #[Test]
    public function titleField(): void
    {
        $response = $this->postJson(self::getEndpointUrl(), ['title' => 111]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must be a string');

        $tooLongString = $this->generateRandomString(BestSeller::TITLE_FIELD_MAX_LENGTH + 1);

        $response = $this->postJson(self::getEndpointUrl(), ['title' => $tooLongString]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must not be greater than');
    }

    #[Test]
    public function isbnField(): void
    {
        $response = $this->postJson(self::getEndpointUrl(), ['isbn' => 111]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must be an array');

        $response = $this->postJson(self::getEndpointUrl(), ['isbn' => [
            111
        ]]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must be a string');

        $response = $this->postJson(self::getEndpointUrl(), ['isbn' => [
            "123456789"
        ]]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field format is invalid');

        $response = $this->postJson(self::getEndpointUrl(), ['isbn' => [
            "12345678901234"
        ]]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field format is invalid');

        $response = $this->postJson(self::getEndpointUrl(), ['isbn' => [
            "123456789A"
        ]]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field format is invalid');

        $response = $this->postJson(self::getEndpointUrl(), ['isbn' => [
            "1234567890",
            "1234567890",
        ]]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field has a duplicate value');
    }

    #[Test]
    public function offsetField(): void
    {
        $response = $this->postJson(self::getEndpointUrl(), ['offset' => "111"]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must be an integer');

        $response = $this->postJson(self::getEndpointUrl(), ['offset' => -1]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must be at least 0');

        $response = $this->postJson(self::getEndpointUrl(), ['offset' => BestSeller::OFFSET_FIELD_STEP + 1]);
        $this->_assertErrorStatusAndMessageContains($response, Response::HTTP_UNPROCESSABLE_ENTITY, 'field must be a multiple of 20');
    }

    #[Test]
    public function validRequest(): void
    {
        $response = $this->postJson(self::getEndpointUrl(), [
            'author' => 'Stephen King',
            'title' => '',
            'isbn' => [],
            'offset' => 20,
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }
}