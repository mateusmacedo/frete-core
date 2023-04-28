<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators\Implementations;

use Frete\Core\Domain\Validators\Validator;

class NumericRangeValidator extends Validator
{
    public function __construct(private int $min, private int $max)
    {
        $this->errorMessage = null;
    }

    public function validate(mixed $input): bool
    {
        if (!is_numeric($input)) {
            $this->errorMessage = 'Invalid numeric value';
            return false;
        }

        if ($input < $this->min || $input > $this->max) {
            $this->errorMessage = "Value must be between {$this->min} and {$this->max}";
            return false;
        }

        return true;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return $this->errorMessage;
    }
}
