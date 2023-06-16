<?php

declare(strict_types=1);

namespace Frete\Core\Application;
use Frete\Core\Domain\Event;

interface Dispatcher
{
    public function dispatch(Action|Event $message): void;
}
