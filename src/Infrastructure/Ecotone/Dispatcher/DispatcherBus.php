<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Dispatcher;

use Ecotone\Messaging\MessagePublisher;
use Ecotone\Modelling\{CommandBus, QueryBus};
use Frete\Core\Application\EventStoreDispatcher;
use Frete\Core\Application\{Action, Command, Dispatcher, Query};
use Frete\Core\Domain\Event;
use Frete\Core\Domain\EventStore;
use Frete\Core\Infrastructure\Errors\InfrastructureError;
use Frete\Core\Infrastructure\Messaging\MetadataStore;
use Frete\Core\Shared\Result;
use Throwable;

class DispatcherBus implements Dispatcher, EventStoreDispatcher
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private readonly MessagePublisher $eventBus
    ) {
    }

    public function dispatch(Action|Event $message, array $messageMetadata = []): Result
    {
        try {
            if ($message instanceof Command) {
                return $this->commandBus->send($message);
            }

            if ($message instanceof Query) {
                return $this->queryBus->send($message);
            }

            if ($message instanceof Event) {
                $this->eventBus->convertAndSendWithMetadata($message, metadata: $messageMetadata);
                return Result::success(true);
            }
        } catch (Throwable $e) {
            return Result::failure(new InfrastructureError($e->getMessage()));
        }

        return Result::failure(new InfrastructureError('Message not supported.'));
    }

	public function dispatchStore(EventStore $store): void
    {
        foreach ($store->getEvents() as $event) {
            $metadata = [];
            if(is_subclass_of($event, MetadataStore::class)) {
                $metadata = $event->getAllMetadata();
            }
            $this->dispatch($event, $metadata);
            $store->commitEvent($event);
        }
	}
}
