<?php

declare(strict_types=1);

namespace FreightPayments\Core\Application;

use ArrayObject;
use BackedEnum;
use Exception;

class ActionFactory implements IActionFactory
{
    private ArrayObject $commandQueryEventMap;

    public function __construct(string $actionsEnumPath)
    {
        $this->commandQueryEventMap = new ArrayObject();
        $this->register($actionsEnumPath);
    }

    public function register(string $actionsEnumPath): void
    {
        if (!enum_exists($actionsEnumPath)) {
            throw new Exception('an enum instance is expected for the action record.');
        }
        foreach ($actionsEnumPath::cases() as $action) {
            $this->commandQueryEventMap->offsetSet($action->name, $action->value);
        }
    }

    public function exists(string $action): bool
    {
        return $this->commandQueryEventMap->offsetExists($action);
    }

    public function create(string|BackedEnum $action, ?array $actionProps = null): Action
    {
        $actionName = ($action instanceof BackedEnum) ? $action->name : $action;
        if (!$this->exists($actionName)) {
            throw new Exception("there is no {$actionName} action on the enum");
        }
        $actionInstance = $this->commandQueryEventMap->offsetGet($actionName);
        return null != $actionProps ? new $actionInstance(...$actionProps) : new $actionInstance();
    }

    public function listActions(): ArrayObject
    {
        return $this->commandQueryEventMap;
    }
}
