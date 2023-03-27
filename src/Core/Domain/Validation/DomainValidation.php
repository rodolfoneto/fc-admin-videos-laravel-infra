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

    public static function strMaxLenght(string $value, int $maxLenght = 255, string $exceptMessage = "")
    {
        if(strlen($value) > $maxLenght) {
            throw new EntityValidationException($exceptMessage ?? "The value must not be greater than {$maxLenght} characters");
        }
    }

    public static function strMinLenght(string $value, int $minLenght = 2, string $exceptMessage = "")
    {
        if(strlen($value) < $minLenght) {
            throw new EntityValidationException($exceptMessage ?? "The value must be greater than {$minLenght} characters");
        }
    }

    public static function strCanNullOrMaxLenght(string $value, int $maxLenght = 255, string $exceptMessage = "")
    {
        if(!empty($value) && strlen($value) > $maxLenght) {
            throw new EntityValidationException($exceptMessage ?? "The value is null or must not be greater than {$maxLenght} characters");
        }
    }
}