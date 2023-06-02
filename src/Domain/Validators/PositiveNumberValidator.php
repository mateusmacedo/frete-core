<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class PositiveNumberValidator extends Validator
{
    private bool $isValid = false;

    public function validate(mixed $input): bool
    {
        $this->isValid = (is_float($input) || is_int($input)) && $input >= 0;
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Not a positive number' : null;
    }
}
