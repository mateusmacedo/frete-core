<?php

declare(strict_types=1);

namespace Frete\Core\Application;

interface IMessageDispatcher
{
    public function dispatch(Action $message): void;
}
