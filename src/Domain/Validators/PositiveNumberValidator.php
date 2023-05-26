<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class PositiveNumberValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return (is_float($input) || is_int($input)) && $input >= 0;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Not a positive number';
    }
}
