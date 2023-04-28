<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use Attribute;
use Frete\Shared\Decorator;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ValidationDecorator implements Decorator
{
    public function __construct(private Validator $validator, private int $parameterIndex)
    {
    }

    public function execute(callable $action, ?array $arguments = [])
    {
        $value = $arguments[$this->parameterIndex];
        if (!$this->validator->validate($value)) {
            throw new InvalidArgumentException("Validation failed for argument {$this->parameterIndex}");
        }

        return $action(...$arguments);
    }
}
