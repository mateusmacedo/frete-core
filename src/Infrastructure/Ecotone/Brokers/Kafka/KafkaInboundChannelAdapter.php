<?php

declare(strict_types=1);

namespace FreightPayments\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\EnqueueInboundChannelAdapter;

final class KafkaInboundChannelAdapter extends EnqueueInboundChannelAdapter
{
    public function initialize(): void
    {
        $context = $this->connectionFactory->createContext();
        $context->createQueue($this->queueName);
    }

    public function connectionException(): string
    {
        return ConnectException::class;
    }
}
