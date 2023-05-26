<?php

declare(strict_types=1);

namespace Frete\Core\Application\Errors;

class RecoverDataError extends ApplicationError
{
    public function __construct(
        private readonly mixed $error
    ) {
    }

    public function getError()
    {
        return $this->error;
    }
}
