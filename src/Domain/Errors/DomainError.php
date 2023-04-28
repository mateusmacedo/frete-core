<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Errors;

use ArrayObject;

class DomainError
{
    public function __construct(private readonly ArrayObject $errors, private readonly ?string $domainName = null)
    {
    }

    public function getErrors(): ArrayObject
    {
        if ($this->domainName) {
            return new ArrayObject([$this->domainName => (array) $this->errors]);
        }

        return $this->errors;
    }
}
