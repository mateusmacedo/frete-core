<?php

declare(strict_types=1);

namespace Frete\Core\Domain;

abstract class AbstractWriterRepository
{
    abstract public function save(mixed $input): mixed;
}
