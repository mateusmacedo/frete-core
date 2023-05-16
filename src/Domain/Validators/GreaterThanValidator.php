<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class GreaterThanValidator extends Validator
{
    public function __construct(private int $min)
    {
        
    }

    public function validate(mixed $input): bool
    {
        return ((is_float($input) || is_int($input)) && ($input > $this->min));
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return "Value must be numeric and greater than {$this->min}";
    }
}
