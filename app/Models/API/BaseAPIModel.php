<?php
namespace App\Models\API;

class BaseAPIModel
{
    /**
     * Get array of field names for validation from an array of rules, excluding rules with an asterisk (like `someArray.*`).
     *
     * @return string[]
     */
    protected static function rulesToFieldNames(array $rules): array
    {
        return array_values(array_filter(array_keys($rules), function ($v) {
            return (!str_contains($v, '*'));
        }));
    }

    /**
     * @param array<string,array> $validationRules
     * @return array<string,string>
     */
    protected static function getFormattedAttributeNames(array $validationRules): array
    {
        $results = [];
        foreach ($validationRules as $fieldName => $validators) {
            $results[$fieldName] = sprintf("`%s`", $fieldName);
        }

        return $results;
    }
}