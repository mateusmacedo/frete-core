<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers;

use Ecotone\Enqueue\{CachedConnectionFactory, OutboundMessageConverter};
use Ecotone\Messaging\{Message, MessageHandler, MessageHeaders};
use Frete\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\IHeaderMessage;
use Interop\Queue\Destination;

abstract class CustomEnqueueOutboundChannelAdapter implements MessageHandler
{
    private bool $initialized = false;

    public function __construct(
        protected CachedConnectionFactory $connectionFactory,
        protected Destination $destination,
        protected bool $autoDeclare,
        protected OutboundMessageConverter $outboundMessageConverter,
        private IHeaderMessage $messageBrokerHeaders
    ) {
    }

    abstract public function initialize(): void;

    public function handle(Message $message): void
    {
        if ($this->autoDeclare && !$this->initialized) {
            $this->initialize();
            $this->initialized = true;
        }

        $outboundMessage = $this->outboundMessageConverter->prepare($message);
        $messageBrokerHeaders = $this->messageBrokerHeaders?->getSchema() ?? [];

        $messageToSend = $this->connectionFactory->createContext()->createMessage(
            $outboundMessage->getPayload(),
            [
                MessageHeaders::CONTENT_TYPE => $outboundMessage->getContentType(),
                $outboundMessage->getHeaders()
            ],
            $messageBrokerHeaders
        );

        $this->connectionFactory->getProducer()
            ->setTimeToLive($outboundMessage->getTimeToLive())
            ->setDeliveryDelay($outboundMessage->getDeliveryDelay())
            ->send($this->destination, $messageToSend);
    }
}
