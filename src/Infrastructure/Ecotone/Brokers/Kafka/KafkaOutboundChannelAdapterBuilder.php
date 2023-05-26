<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\{CachedConnectionFactory, EnqueueOutboundChannelAdapterBuilder, HttpReconnectableConnectionFactory, OutboundMessageConverter};
use Ecotone\Messaging\Conversion\ConversionService;
use Ecotone\Messaging\Handler\{ChannelResolver, ReferenceSearchService};
use Ecotone\Messaging\MessageConverter\DefaultHeaderMapper;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Frete\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\DefaultMessageHeader;

class KafkaOutboundChannelAdapterBuilder extends EnqueueOutboundChannelAdapterBuilder
{
    private function __construct(private string $topicName, private string $connectionFactoryReferenceName, private string $messageBrokerHeadersReferenceName)
    {
        $this->initialize($connectionFactoryReferenceName);
    }

    public static function create(string $topicName, string $connectionFactoryReferenceName = RdKafkaConnectionFactory::class, string $messageBrokerHeadersReferenceName = DefaultMessageHeader::class): self
    {
        return new self($topicName, $connectionFactoryReferenceName, $messageBrokerHeadersReferenceName);
    }

    public function build(ChannelResolver $channelResolver, ReferenceSearchService $referenceSearchService): KafkaOutboundChannelAdapter
    {
        /** @var RdKafkaConnectionFactory $connectionFactory */
        $connectionFactory = $referenceSearchService->get($this->connectionFactoryReferenceName);
        /** @var ConversionService $conversionService */
        $conversionService = $referenceSearchService->get(ConversionService::REFERENCE_NAME);

        // call the headers HERE!
        $messageBrokerHeadersReferenceName = new ($this->messageBrokerHeadersReferenceName)();

        $headerMapper = DefaultHeaderMapper::createWith([], $this->headerMapper, $conversionService);
        return new KafkaOutboundChannelAdapter(
            CachedConnectionFactory::createFor(new HttpReconnectableConnectionFactory($connectionFactory)),
            $this->topicName,
            $this->autoDeclare,
            new OutboundMessageConverter($headerMapper, $conversionService, $this->defaultConversionMediaType, $this->defaultDeliveryDelay, $this->defaultTimeToLive, $this->defaultPriority, []),
            $messageBrokerHeadersReferenceName
        );
    }
}
