<?php

declare(strict_types=1);

namespace Frete\Core\Domain;

abstract class AggregateRoot extends Entity
{
    private BaseArrayObject $domainEvents;

    public function __construct(string $id)
    {
        parent::__construct($id);
        $this->clearEvents();
    }

    public function clearEvents(): void
    {
        $this->domainEvents = new BaseArrayObject();
    }

    public function getEvents(): array
    {
        return $this->domainEvents->getArrayCopy();
    }

    protected function addEvent(DomainEvent $event)
    {
        $this->domainEvents->append($event);
    }
}
