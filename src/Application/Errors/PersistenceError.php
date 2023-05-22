<?php

declare(strict_types=1);

namespace Frete\Core\Application\Errors;

use Frete\Core\Application\Errors\ApplicationError;

class PersistenceError extends ApplicationError
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