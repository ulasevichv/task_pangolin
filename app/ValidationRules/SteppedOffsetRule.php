<?php
namespace App\ValidationRules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SteppedOffsetRule implements ValidationRule
{
    private int $stepSize;

    public function __construct(int $stepSize)
    {
        $this->stepSize = $stepSize;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_int($value)) {
            $fail("The `{$attribute}` field must be an integer.");
            return;
        }

        if ($value < 0) {
            $fail("The `{$attribute}` field must be at least 0.");
        }

        if ($value % $this->stepSize !== 0) {
            $fail(sprintf("The `{$attribute}` field must be a multiple of %d.", $this->stepSize));
        }
    }
}