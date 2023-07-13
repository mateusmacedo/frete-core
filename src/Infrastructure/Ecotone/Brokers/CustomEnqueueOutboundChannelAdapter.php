<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers;

use Ecotone\Enqueue\OutboundMessage;
use Ecotone\Enqueue\{CachedConnectionFactory, OutboundMessageConverter};
use Ecotone\Messaging\{Message, MessageHandler, MessageHeaders};
use Frete\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\IHeaderMessage;
use Interop\Queue\{Destination, Message as buildMessageReturn};

abstract class CustomEnqueueOutboundChannelAdapter implements MessageHandler
{
    private OutboundMessage $outboundMessage;
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
        $messageToSend = $this->buildMessage($message);
        $this->connectionFactory->getProducer()
            ->setTimeToLive($this->outboundMessage->getTimeToLive())
            ->setDeliveryDelay($this->outboundMessage->getDeliveryDelay())
            ->send($this->destination, $messageToSend)
        ;
    }

    protected function buildMessage(Message $message): buildMessageReturn
    {
        $this->outboundMessage = $outboundMessage = $this->outboundMessageConverter->prepare($message);
        $headers = $outboundMessage->getHeaders();
        $headers[MessageHeaders::CONTENT_TYPE] = $outboundMessage->getContentType();

        $messageBrokerHeaders = $this->enrichHeaderMessageBroker($this->messageBrokerHeaders->getSchema(), $headers, $message->getPayload());

        return $this->connectionFactory->createContext()->createMessage($outboundMessage->getPayload(), $headers, $messageBrokerHeaders);
    }

    protected function enrichHeaderMessageBroker(array $headersBrokerSchema, array $messageProperties, mixed $messagePayload)
    {
        if ((isset($messageProperties['__TypeId__']) && !empty($messageProperties['__TypeId__'])) && !isset($headersBrokerSchema['eventType'])) {
            $transformToEventType = str_replace('\\', '.', $messageProperties['__TypeId__']);
            $headersBrokerSchema['EventType'] = $transformToEventType;
        }

        if ((isset($messageProperties['version']) && !empty($messageProperties['version'])) && !isset($headersBrokerSchema['version'])) {
            $headersBrokerSchema['version'] = $messagePayload->version;
        }

        return $headersBrokerSchema;
    }
}
