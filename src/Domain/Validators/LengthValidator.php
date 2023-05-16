<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class LengthValidator extends Validator
{
    public function __construct(private int $minLength, private int $maxLength)
    {
       
    }

    public function validate(mixed $input): bool
    {
        $inputLen = strlen($input);
        return ($inputLen >= $this->minLength || $inputLen <= $this->maxLength);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid length';
    }
}
