<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use Traversable;

class AttributeValidator extends Validator
{
    private ?string $errorMessage = null;
    public function __construct(public readonly string $attribute, protected ?Validator $validator = null)
    {
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }

    public function setValidator(Validator $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function validate(mixed $input): bool
    {
        if (is_object($input) && !isset($input->{$this->attribute})) {
            return true;
        }

        if (is_array($input) && !isset($input[$this->attribute])) {
            return true;
        }

        if (is_object($input) && isset($input->{$this->attribute})) {
            $input = $input->{$this->attribute};
        }

        if (is_array($input) && isset($input[$this->attribute])) {
            $input = $input[$this->attribute];
        }

        if (is_array($input) || $input instanceof Traversable) {
            $result = true;
            foreach ($input as $value) {
                if (!$this->validator->validate($value)) {
                    $result = false;
                }
            }
            return $result;
        }

        return $this->validator->validate($input);
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->validator->getErrorMessage();
    }
}
