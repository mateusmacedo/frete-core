<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use ArrayObject;
use Traversable;

class ValidatorComposite extends Validator
{
    public function __construct(
        protected ArrayObject $validators = new ArrayObject(),
        protected ArrayObject $errorMessage = new ArrayObject()
    ) {
    }

    /**
     * @param Validator $validator
     */
    public function addValidator(Validator $validator): void
    {
        $this->validators->append($validator);
    }

    public function getValidators(): ArrayObject
    {
        return $this->validators;
    }

    /**
     * @return array
     */
    public function getErrorMessage(): array
    {
        return $this->errorMessage->getArrayCopy();
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function validate(mixed $input): bool
    {
        $this->errorMessage->exchangeArray([]);

        if (is_array($input) || $input instanceof Traversable) {
            return $this->validateCollection($input);
        }

        foreach ($this->validators as $validator) {
            if (!$validator->validate($input)) {
                $this->errorMessage->append($validator->getErrorMessage());
            }
        }

        return 0 === $this->errorMessage->count();
    }

    protected function validateCollection(array|Traversable $input): bool
    {
        $validatorIterator = $this->validators->getIterator();
        foreach ($input as $key => $value) {
            foreach ($validatorIterator as $validator) {
                if (!$validator->validate($value)) {
                    $previousErrorMessage = $this->errorMessage->offsetGet($key) ?? [];
                    $errorMessage = $validator->getErrorMessage();
                    $previousErrorMessage[] = $errorMessage;
                    $this->errorMessage->offsetSet($key, $previousErrorMessage);
                }
            }
        }
        return 0 === $this->errorMessage->count();
    }
}
