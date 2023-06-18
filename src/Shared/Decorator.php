<?php

declare(strict_types=1);

namespace Frete\Core\Shared;

interface Decorator
{
    public function execute(callable $callback, array $parameters = []);
}
