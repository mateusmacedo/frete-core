<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class BooleanValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return is_bool($input);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid boolean';
    }
}
