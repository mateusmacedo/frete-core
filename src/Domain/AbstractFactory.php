<?php

declare(strict_types=1);

namespace Frete\Core\Domain;

abstract class AbstractFactory
{
    protected object $item;

    abstract public function create(string $target, mixed $data = null): mixed;

    abstract protected function reset(): void;
}
