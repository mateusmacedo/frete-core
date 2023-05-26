<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class LessThanValidator extends Validator
{
    public function __construct(private int $max)
    {
    }

    public function validate(mixed $input): bool
    {
        return (is_float($input) || is_int($input)) && $input < $this->max;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return "The value must be numeric and less than {$this->max}";
    }
}
