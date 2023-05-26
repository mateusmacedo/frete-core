<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\{CachedConnectionFactory, EnqueueInboundChannelAdapterBuilder, HttpReconnectableConnectionFactory, InboundMessageConverter};
use Ecotone\Messaging\Conversion\ConversionService;
use Ecotone\Messaging\Endpoint\PollingMetadata;
use Ecotone\Messaging\Handler\{ChannelResolver, ReferenceSearchService};
use Ecotone\Messaging\MessageConverter\DefaultHeaderMapper;
use Enqueue\RdKafka\RdKafkaConnectionFactory;

final class KafkaInboundChannelAdapterBuilder extends EnqueueInboundChannelAdapterBuilder
{
    public static function createWith(string $endpointId, string $topicName, ?string $requestChannelName, string $connectionReferenceName = RdKafkaConnectionFactory::class): self
    {
        return new self($topicName, $endpointId, $requestChannelName, $connectionReferenceName);
    }

    public function createInboundChannelAdapter(ChannelResolver $channelResolver, ReferenceSearchService $referenceSearchService, PollingMetadata $pollingMetadata): KafkaInboundChannelAdapter
    {
        /** @var RdKafkaConnectionFactory $connectionFactory */
        $connectionFactory = $referenceSearchService->get($this->connectionReferenceName);
        /** @var ConversionService $conversionService */
        $conversionService = $referenceSearchService->get(ConversionService::REFERENCE_NAME);

        $headerMapper = DefaultHeaderMapper::createWith($this->headerMapper, [], $conversionService);

        return new KafkaInboundChannelAdapter(
            CachedConnectionFactory::createFor(new HttpReconnectableConnectionFactory($connectionFactory)),
            $this->buildGatewayFor($referenceSearchService, $channelResolver, $pollingMetadata),
            $this->declareOnStartup,
            $this->messageChannelName,
            $this->receiveTimeoutInMilliseconds,
            new InboundMessageConverter($this->getEndpointId(), $this->acknowledgeMode, $headerMapper)
        );
    }
}
