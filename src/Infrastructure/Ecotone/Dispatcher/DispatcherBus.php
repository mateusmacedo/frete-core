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

    public function dispatch(Action|Event $message): Result
    {
        try {
            if (is_a($message, Command::class)) {
                return $this->commandBus->send($message);
            }

            if (is_a($message, Query::class)) {
                return $this->queryBus->send($message);
            }

            if (is_a($message, Event::class)) {
                if (is_a($message, MetadataStore::class)) {
                    $this->eventBus->convertAndSendWithMetadata($message, $message->getAllMetadata());
                } else {
                    $this->eventBus->convertAndSend($message);
                }
                return Result::success(true);
            }
        } catch (Throwable $e) {
            return Result::failure(new InfrastructureError($e->getMessage()));
        }

        return Result::failure(new InfrastructureError('Message not supported.'));
    }

	public function dispatchStore(EventStore $store): Result
    {
        foreach ($store->getEvents() as $event) {
            $this->dispatch($event);
            $store->commitEvent($event);
        }
        return Result::success(true);
	}
}
