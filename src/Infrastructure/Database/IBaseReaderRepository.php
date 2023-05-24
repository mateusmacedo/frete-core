<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Database;

use Frete\Core\Domain\Entity;
use Frete\Core\Infrastructure\Database\Contracts\{ListProps, ListResponse};
use Frete\Core\Infrastructure\Errors\InfrastructureError;

interface IBaseReaderRepository
{
    public function list(ListProps $props): ListResponse|InfrastructureError;

    public function findOne(array $filter): Entity|InfrastructureError|null;
}
