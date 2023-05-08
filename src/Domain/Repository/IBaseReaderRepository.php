<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Repository;

use Frete\Core\Domain\Entity;
use Frete\Core\Infrastructure\Database\Contracts\{ListProps, ListResponse};
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;

interface IBaseReaderRepository
{
    public function list(ListProps $props): ListResponse|RepositoryError;

    public function findOne(array $filter): Entity|RepositoryError|null;
}
