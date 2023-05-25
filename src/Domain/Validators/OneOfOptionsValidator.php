<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class OneOfOptionsValidator extends Validator
{
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
        return in_array($input, $this->validOptions, strict: true);
    }

      /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid option';
    }
}
