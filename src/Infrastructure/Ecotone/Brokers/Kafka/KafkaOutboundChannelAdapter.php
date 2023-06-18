<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\{CachedConnectionFactory, OutboundMessageConverter};
use Ecotone\Messaging\Message;
use Enqueue\RdKafka\RdKafkaTopic;
use Frete\Core\Infrastructure\Ecotone\Brokers\CustomEnqueueOutboundChannelAdapter;
use Frete\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\IHeaderMessage;
use Interop\Queue\Message as buildMessageReturn;

final class KafkaOutboundChannelAdapter extends CustomEnqueueOutboundChannelAdapter
{
    public function __construct(CachedConnectionFactory $connectionFactory, private RdKafkaTopic $topic, bool $autoDeclare, OutboundMessageConverter $outboundMessageConverter, IHeaderMessage $messageBrokerHeaders)
    {
        parent::__construct(
            $connectionFactory,
            $topic,
            $autoDeclare,
            $outboundMessageConverter,
            $messageBrokerHeaders
        );
    }

    public function initialize(): void
    {
        $context = $this->connectionFactory->createContext();
        $context->createQueue($this->topic->getTopicName());
    }

    public function buildMessage(Message $message): buildMessageReturn
    {
        $message = parent::buildMessage($message);
        $props = $message->getProperties();

        if (isset($props['partition']) && is_int($props['partition'])) {
            // @phpstan-ignore-next-line
            $message->setPartition($props['partition']);
        }

        if (isset($props['key']) && is_int($props['key'])) {
            // @phpstan-ignore-next-line
            $message->setKey($props['key']);
        }

        return $message;
    }
}
