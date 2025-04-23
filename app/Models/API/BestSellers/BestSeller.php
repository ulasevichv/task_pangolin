<?php
namespace App\Models\API\BestSellers;

use App\ExternalServices\NewYorkTimes\BooksAPI;
use App\Models\API\BaseAPIModel;
use App\ValidationRules\SteppedOffsetRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BestSeller extends BaseAPIModel
{
    public const AUTHOR_FIELD_MAX_LENGTH = 255;
    public const TITLE_FIELD_MAX_LENGTH = 255;
    public const ISBN_FIELD_REGEX = '/^([0-9]{10}|[0-9]{13})$/';
    public const OFFSET_FIELD_STEP = 20;

    private BooksAPI $booksAPI;

    public function __construct(BooksAPI $booksAPI)
    {
        $this->booksAPI = $booksAPI;
    }

    private static function getValidationRules(string $fieldName): array
    {
        return match ($fieldName) {
            'author' => ['string', 'max:' . static::AUTHOR_FIELD_MAX_LENGTH],
            'title' => ['string', 'max:' . static::TITLE_FIELD_MAX_LENGTH],
            'isbn' => ['array'],
            'isbn.*' => ['string', 'regex:' . static::ISBN_FIELD_REGEX],
            'offset' => [new SteppedOffsetRule(static::OFFSET_FIELD_STEP)],
            default => throw new \Exception(sprintf("Invalid field name: %s", $fieldName)),
        };
    }

    public function search(Request $request): object
    {
        $validationRules = [
            'author' => array_merge(['nullable'], static::getValidationRules('author')),
            'title' => array_merge(['nullable'], static::getValidationRules('title')),
            'isbn' => array_merge(['nullable'], static::getValidationRules('isbn')),
            'isbn.*' => array_merge(['distinct'], static::getValidationRules('isbn.*')),
            'offset' => array_merge(['nullable'], static::getValidationRules('offset')),
        ];
        $fieldNames = static::rulesToFieldNames($validationRules);

        Validator::make($request->only($fieldNames), $validationRules, [], static::getFormattedAttributeNames($validationRules))->validate();

        $validatedData = (object)$request->only($fieldNames);

        $serviceResponseObj = $this->booksAPI->search(
            ($validatedData->author ?? ''),
            ($validatedData->title ?? ''),
            ($validatedData->isbn ?? []),
            ($validatedData->offset ?? 0)
        );

        return (object)[
            'num_results' => $serviceResponseObj->num_results,
            'results' => $serviceResponseObj->results,
        ];
    }
}