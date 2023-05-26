<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Database;

use Frete\Core\Domain\Entity;
use Frete\Core\Infrastructure\Errors\InfrastructureError;

interface IBaseWriterRepository
{
    public function upsert(Entity $data): InfrastructureError|bool;

    public function remove(array $filter): InfrastructureError|bool;
}
