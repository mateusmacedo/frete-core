<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Database\Errors;

use Frete\Core\Infrastructure\Errors\InfrastructureError;

class RepositoryError extends InfrastructureError
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
