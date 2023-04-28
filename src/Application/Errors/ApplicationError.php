<?php

declare(strict_types=1);

namespace Frete\Core\Application\Errors;

use Frete\Core\Shared\Result;

class ApplicationError extends Result
{
    public function __construct($errors)
    {
        parent::__construct(false, null, $errors);
    }
}
