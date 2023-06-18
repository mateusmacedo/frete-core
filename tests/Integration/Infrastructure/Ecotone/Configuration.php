<?php

declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Ecotone;

use Ecotone\Messaging\Attribute\ServiceContext;
use Ecotone\Messaging\Conversion\MediaType;
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\KafkaBackedMessageChannelBuilder;

class MessagingConfiguration
{
    #[ServiceContext]
    public function enableKafka()
    {
        return KafkaBackedMessageChannelBuilder::create($_ENV['KAFKA_TOPIC'])
            ->withDefaultConversionMediaType(MediaType::APPLICATION_JSON);
    }
}
