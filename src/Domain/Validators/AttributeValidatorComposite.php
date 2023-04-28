<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use ArrayObject;

class AttributeValidatorComposite extends ValidatorComposite
{
    public function __construct(private string $attribute)
    {
        $this->errorMessage = [];
        $this->validators = new ArrayObject();
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @param Validator $validator
     */
    public function addValidator(Validator $validator): void
    {
        $this->validators->append($validator);
    }

    /**
     * @return ArrayObject
     */
    public function getValidators(): ArrayObject
    {
        return $this->validators;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function validate(mixed $input): bool
    {
        foreach ($input as $key => $value) {
            if ($key === $this->getAttribute()) {
                $errors = [];
                foreach ($this->getValidators() as $validator) {
                    if (!$validator->validate($value)) {
                        if (is_array($validator->getErrorMessage())) {
                            $errors = array_merge($errors, $validator->getErrorMessage());
                        } else {
                            $errors[] = $validator->getErrorMessage();
                        }
                    }
                }
                if (!empty($errors)) {
                    $this->errorMessage[$this->getAttribute()] = $errors;
                }
            }
        }
        return empty($this->errorMessage);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return $this->errorMessage;
    }
}
