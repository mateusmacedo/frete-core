<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use ArrayObject;

abstract class ValidatorComposite extends Validator
{
    protected ArrayObject $validators;

    abstract public function addValidator(Validator $validator): void;

    abstract public function getValidators(): ArrayObject;
}
