<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators\Implementations;

use Frete\Core\Domain\Validators\Validator;

class LengthValidator extends Validator
{
    private $minLength;
    private $maxLength;

    public function __construct($minLength, $maxLength)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    public function validate(mixed $input): bool
    {
        $this->errorMessage = null;
        if (strlen($input) < $this->minLength || strlen($input) > $this->maxLength) {
            return false;
        }
        return true;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid length';
    }
}
