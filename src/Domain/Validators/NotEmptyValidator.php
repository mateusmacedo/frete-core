<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class NotEmptyValidator extends Validator
{
    private ?bool $isValid = null;

    public function validate(mixed $input): bool
    {
        $this->isValid = !empty($input);
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Cannot be empty' : null;
    }
}
