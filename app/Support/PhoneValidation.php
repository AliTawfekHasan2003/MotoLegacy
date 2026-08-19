<?php

namespace App\Support;

class PhoneValidation
{
    public const MIN_LENGTH = 7;

    public static function rules(array $prefix = ['required'], array $suffix = []): array
    {
        return array_merge($prefix, [
            'string',
            'min:' . self::MIN_LENGTH,
        ], $suffix);
    }
}
