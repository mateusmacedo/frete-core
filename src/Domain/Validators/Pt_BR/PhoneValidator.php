<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators\Pt_BR;

use Frete\Core\Domain\Validators\Validator;

class PhoneValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return (bool) preg_match('/^\\+[0-9]{2}\\([0-9]{2}\\)[0-9]?[0-9]{4}-[0-9]{4}$/', $input);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid phone number';
    }
}
