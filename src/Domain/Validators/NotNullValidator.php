<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class NotNullValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return null !== $input;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Cannot be null';
    }
}
