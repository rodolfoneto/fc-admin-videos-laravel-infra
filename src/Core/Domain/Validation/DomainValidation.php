<?php

namespace Core\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;

class DomainValidation
{
    public static function notNull(string $value, string $exceptMessage = "")
    {
        if (empty($value)) {
            throw new EntityValidationException($exceptMessage ?? "Field not be empty");
        }
    }

    public static function strMaxLength(string $value, int $maxLength = 255, string $exceptMessage = "")
    {
        if(strlen($value) > $maxLength) {
            throw new EntityValidationException($exceptMessage ?? "The value must not be greater than {$maxLength} characters");
        }
    }

    public static function strMinLength(string $value, int $minLength = 2, string $exceptMessage = "")
    {
        if(strlen($value) < $minLength) {
            throw new EntityValidationException($exceptMessage ?? "The value must be greater than {$minLength} characters");
        }
    }

    public static function strCanNullOrMaxLength(string $value, int $maxLength = 255, string $exceptMessage = "")
    {
        if(!empty($value) && strlen($value) > $maxLength) {
            throw new EntityValidationException($exceptMessage ?? "The value is null or must not be greater than {$maxLength} characters");
        }
    }
}
