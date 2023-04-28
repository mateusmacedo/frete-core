<?php

declare(strict_types=1);

namespace Frete\Core\Application;

use Frete\Core\Domain\DomainEvent;

interface Broker
{
    public function publish(DomainEvent $event): void;

    public function subscribe(string $eventClass, Handler $handler): void;
}
