<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class PriceValidation
{
    /** decimal(15, 2) => up to 13 digits before the decimal point. */
    public const MIN = 8;

    public const MAX = 9999999999999.99;

    private const DECIMAL_PATTERN = '/^\d{1,13}(\.\d{1,2})?$/';

    public static function rules(array $prefix = ['nullable']): array
    {
        return array_merge($prefix, self::baseRules());
    }

    public static function filterRules(): array
    {
        return [
            'nullable',
            'numeric',
            'min:0',
            'max:' . self::MAX,
            'regex:' . self::DECIMAL_PATTERN,
        ];
    }

    public static function assertValid(mixed $value, string $attribute = 'price'): void
    {
        if (! self::isValid($value)) {
            throw ValidationException::withMessages([
                $attribute => [
                    'The ' . str_replace('_', ' ', $attribute) . ' must be a number between '
                    . self::MIN . ' and ' . self::MAX . ' with at most 2 decimal places.',
                ],
            ]);
        }
    }

    public static function isValid(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (! is_numeric($value)) {
            return false;
        }

        $numeric = (float) $value;

        if ($numeric < self::MIN || $numeric > self::MAX) {
            return false;
        }

        $normalized = number_format($numeric, 2, '.', '');

        return (bool) preg_match(self::DECIMAL_PATTERN, $normalized);
    }

    private static function baseRules(): array
    {
        return [
            'numeric',
            'min:' . self::MIN,
            'max:' . self::MAX,
            'regex:' . self::DECIMAL_PATTERN,
        ];
    }
}
