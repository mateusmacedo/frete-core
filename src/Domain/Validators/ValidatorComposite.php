<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use ArrayObject;
use Traversable;

class ValidatorComposite extends Validator
{
    private ArrayObject $errorMessage;

    public function __construct(public readonly ArrayObject $validators = new ArrayObject())
    {
        $this->errorMessage = new ArrayObject();
    }

    /**
     * @param Validator $validator
     */
    public function addValidator(Validator $validator): void
    {
        $this->validators->append($validator);
    }

    /**
     * @return array
     */
    public function getErrorMessage(): array
    {
        return $this->errorMessage->getArrayCopy();
    }

    protected function validateCollection(array | Traversable $input, ArrayObject $errors): ArrayObject
    {
        foreach ($input as $key => $value) {
            $valueErrorMessages = new ArrayObject();
            foreach ($this->validators as $validator) {
                if (!$validator->validate([$key => $value])) {
                    $valueErrorMessages->append($validator->getErrorMessage());
                }
            }
            if ($valueErrorMessages->count() > 0) {
                $errors->append($valueErrorMessages->getArrayCopy());
            }
        }
        return $errors;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function validate(mixed $input): bool
    {
        $this->errorMessage = new ArrayObject();

        if (is_array($input) || $input instanceof Traversable) {
            $collectionError = $this->validateCollection($input, $this->errorMessage);
            return 0 === $collectionError->count();
        }
        foreach ($this->validators as $validator) {
            if (!$validator->validate($input)) {
                $this->errorMessage->append($validator->getErrorMessage());
            }
        }
        return 0 === $this->errorMessage->count();
    }
}
