<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class OptionalStringValidator extends Validator
{
    private bool $isValid = false;

    public function validate(mixed $input): bool
    {
        if (null === $input) {
            $this->isValid = true;
            return $this->isValid;
        }
        $this->isValid = (new StringValidator())->validate($input);
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Invalid string' : null;
    }
}
