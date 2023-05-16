<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class FloatValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return is_float($input);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid float number';
    }
}
