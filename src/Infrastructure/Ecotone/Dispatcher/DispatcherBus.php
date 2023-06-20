<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Dispatcher;

use Ecotone\Modelling\CommandBus;
use Ecotone\Modelling\EventBus;
use Ecotone\Modelling\QueryBus;
use Frete\Core\Application\Action;
use Frete\Core\Application\Command;
use Frete\Core\Application\Dispatcher;
use Frete\Core\Application\Query;
use Frete\Core\Domain\Event;
use Frete\Core\Infrastructure\Errors\InfrastructureError;
use Frete\Core\Shared\Result;
use Throwable;

class DispatcherBus implements Dispatcher
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private readonly EventBus $eventBus
    ) {
    }

    public function dispatch(Action|Event $message): Result
    {
        try{
            if ($message instanceof Command) {
                return $this->commandBus->send($message);
            }

            if ($message instanceof Query) {
                return $this->queryBus->send($message);
            }

            if ($message instanceof Event) {
                $this->eventBus->publish($message);
                return Result::success(true);
            }
        } catch (Throwable $e) {
            return Result::failure(new InfrastructureError($e->getMessage()));
        }


        return Result::failure(new InfrastructureError('Message not supported.'));
    }
}
