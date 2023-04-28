<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Database;

use Frete\Core\Domain\Entity;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;

interface IBaseWriterRepository
{
    public function upsert(Entity $data): RepositoryError|bool;

    public function remove(array $filter): RepositoryError|bool;
}
