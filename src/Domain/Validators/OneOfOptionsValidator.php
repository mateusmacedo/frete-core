<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class OneOfOptionsValidator extends Validator
{
    private ?bool $isValid = null;

    public function __construct(private array $validOptions = [])
    {
    }

    public function setOptions(array $options): mixed
    {
        $this->validOptions = $options;
        return $this;
    }

    public function validate(mixed $input): bool
    {
        $this->isValid = in_array($input, $this->validOptions, strict: true);
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Invalid option' : null;
    }
}
