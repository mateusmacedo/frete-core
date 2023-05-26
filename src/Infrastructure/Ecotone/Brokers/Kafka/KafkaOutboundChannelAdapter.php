<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\{CachedConnectionFactory, OutboundMessageConverter};
use Enqueue\RdKafka\RdKafkaTopic;
use Frete\Core\Infrastructure\Ecotone\Brokers\CustomEnqueueOutboundChannelAdapter;
use Frete\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\IHeaderMessage;

final class KafkaOutboundChannelAdapter extends CustomEnqueueOutboundChannelAdapter
{
    public function __construct(CachedConnectionFactory $connectionFactory, private string $topicName, bool $autoDeclare, OutboundMessageConverter $outboundMessageConverter, IHeaderMessage $messageBrokerHeaders)
    {
        parent::__construct(
            $connectionFactory,
            new RdKafkaTopic($topicName),
            $autoDeclare,
            $outboundMessageConverter,
            $messageBrokerHeaders
        );
    }

    public function initialize(): void
    {
        $context = $this->connectionFactory->createContext();
        $context->createQueue($this->topicName);
    }
}
