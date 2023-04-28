<?php

declare(strict_types=1);

namespace Frete\Core\Application;

use ArrayObject;

interface IActionFactory
{
    public function register(string $actionsEnumPath): void;

    public function exists(string $action): bool;

    public function create(string $action, ?array $actionProps = null);

    public function listActions(): ArrayObject;
}
