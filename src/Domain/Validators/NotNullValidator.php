<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class NotNullValidator extends Validator
{
    private bool $isValid = false;

    public function validate(mixed $input): bool
    {
        $this->isValid = null !== $input;
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Cannot be null' : null;
    }
}
