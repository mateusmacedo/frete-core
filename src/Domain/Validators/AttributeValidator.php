<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

class AttributeValidator extends Validator
{
    public function __construct(private string $attribute, private Validator $validator)
    {
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getValidator(): Validator|ValidatorComposite
    {
        return $this->validator;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function validate(mixed $input): bool
    {
        return $this->getValidator()->validate($input);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return $this->validator->getErrorMessage();
    }
}
