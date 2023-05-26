<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class EmailValidator extends Validator
{
    private ?bool $isValid = null;

    public function validate(mixed $input): bool
    {
        $this->isValid = (bool) filter_var($input, FILTER_VALIDATE_EMAIL);
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Invalid email address' : null;
    }
}
