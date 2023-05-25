<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use ArrayObject;
use Traversable;

class AttributeValidator extends Validator
{
    protected string|array|ArrayObject|null $errorMessage;
    public function __construct(
        public readonly string $attribute,
        protected ?Validator $validator = null
    ) {
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }

    public function setValidator(Validator $validator): void
    {
        $this->validator = $validator;
    }

    public function getErrorMessage()
    {
        $errorMessage = null;
        if (is_array($this->errorMessage)) {
            $errorMessage = $this->errorMessage;
        }
        if ($this->errorMessage instanceof ArrayObject) {
            $errorMessage = $this->errorMessage->getArrayCopy();
        }
        if (is_string($this->errorMessage)) {
            $errorMessage = $this->errorMessage;
        }
        if (empty($errorMessage)) {
            return null;
        }
        $result = [$this->attribute => $errorMessage];
        return $result;
    }

    public function validate(mixed $input): bool
    {
        if (is_object($input) && isset($input->{$this->attribute})) {
            $input = $input->{$this->attribute};
        }

        if (is_array($input) && isset($input[$this->attribute])) {
            $input = $input[$this->attribute];
        }

        $this->errorMessage = null;

        if (is_array($input) || $input instanceof Traversable) {
            return $this->validateCollection($input);
        }

        if (!$this->validator->validate($input)) {
            $this->errorMessage = $this->validator->getErrorMessage();
        }

        return $this->getErrorMessage()[$this->attribute] === null;
    }

    protected function validateCollection(array|Traversable $input): bool
    {
        $this->errorMessage = new ArrayObject();
        foreach ($input as $key => $value) {
            if (!$this->validator->validate($value)) {

                $previousErrorMessage = $this->errorMessage->offsetGet($key);
                $errorMessage = $this->validator->getErrorMessage();

                if (is_array($previousErrorMessage)){
                    if (is_array($errorMessage)) {
                        $previousErrorMessage = array_merge($previousErrorMessage, $errorMessage);
                    } else {
                        $previousErrorMessage[] = $errorMessage;
                    }
                } else {
                    if (is_null($previousErrorMessage)) {
                        $previousErrorMessage = $errorMessage;
                    } else {
                        $previousErrorMessage = [$previousErrorMessage, $errorMessage];
                    }
                }

                $this->errorMessage->offsetSet($key, $previousErrorMessage);
            }
        }

        return 0 === $this->errorMessage->count();
    }
}
