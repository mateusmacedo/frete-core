<?php

declare(strict_types=1);

namespace FreightPayments\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\EnqueueMessageChannelBuilder;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use FreightPayments\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\DefaultMessageHeader;

final class KafkaBackedMessageChannelBuilder extends EnqueueMessageChannelBuilder
{
    private function __construct(string $topicName, string $connectionReferenceName, string $messageBrokerHeadersReferenceName)
    {
        parent::__construct(
            KafkaInboundChannelAdapterBuilder::createWith(
                $topicName,
                $topicName,
                null,
                $connectionReferenceName
            ),
            KafkaOutboundChannelAdapterBuilder::create(
                $topicName,
                $connectionReferenceName,
                $messageBrokerHeadersReferenceName
            )
        );
    }

    public static function create(string $topicName, string $connectionReferenceName = RdKafkaConnectionFactory::class, string $messageBrokerHeadersReferenceName = DefaultMessageHeader::class): self
    {
        return new self($topicName, $connectionReferenceName, $messageBrokerHeadersReferenceName);
    }
}
