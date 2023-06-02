<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class BooleanValidator extends Validator
{
    private bool $isValid = false;

    public function validate(mixed $input): bool
    {
        $this->isValid = is_bool($input);
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Invalid boolean' : null;
    }
}
