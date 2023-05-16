<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class EmailValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid email address';
    }
}
