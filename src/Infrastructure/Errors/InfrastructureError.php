<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Errors;

use Frete\Core\Shared\Result;

class InfrastructureError extends Result
{
    public function __construct($errors)
    {
        parent::__construct(false, null, $errors);
    }
}
