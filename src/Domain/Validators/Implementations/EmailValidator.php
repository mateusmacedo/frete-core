<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators\Implementations;

use Frete\Core\Domain\Validators\Validator;

class EmailValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid email address';
    }
}
