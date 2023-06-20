<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone;

use ArrayObject;
use Ecotone\Messaging\MessagePublisher;
use Frete\Core\Application\Action;
use Frete\Core\Application\Dispatcher;
use Frete\Core\Domain\Event;

final class EcotoneMessageDispatcher implements Dispatcher
{
    public function __construct(private MessagePublisher $publisher){}

    public function dispatch(Action|Event $message, array $messageMetadata = []): void
    {
        $this->publisher->convertAndSendWithMetadata($message, metadata: $messageMetadata);
    }

    public function bulkDispatch(array|ArrayObject $messages): void
    {
        foreach ($messages as $message) {
            $metadata = array_key_exists('metadata', $message) ? $message['metadata'] : [];
            if (!$message['message'])
                continue;

            $this->dispatch($message['message'], $metadata);
        }
    }
}
