<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class IntegerValidator extends Validator
{
    private ?bool $isValid = null;

    public function validate(mixed $input): bool
    {
        $this->isValid = is_int($input);
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Invalid integer number' : null;
    }
}
