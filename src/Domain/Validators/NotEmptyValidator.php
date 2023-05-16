<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class NotEmptyValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return !empty($input);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Cannot be empty';
    }
}
