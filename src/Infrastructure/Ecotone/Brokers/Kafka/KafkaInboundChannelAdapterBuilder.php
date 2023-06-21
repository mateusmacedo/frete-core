<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\{EnqueueInboundChannelAdapterBuilder, InboundMessageConverter};
use Ecotone\Messaging\Conversion\ConversionService;
use Ecotone\Messaging\Endpoint\PollingMetadata;
use Ecotone\Messaging\Handler\{ChannelResolver, ReferenceSearchService};
use Ecotone\Messaging\MessageConverter\DefaultHeaderMapper;
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Configuration\KafkaTopicConfiguration;
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Connection\KafkaConnectionFactory;

final class KafkaInboundChannelAdapterBuilder extends EnqueueInboundChannelAdapterBuilder
{
    public function __construct(string $messageChannelName, string $endpointId, ?string $requestChannelName, string $connectionReferenceName, private ?KafkaTopicConfiguration $topicConfig)
    {
        parent::__construct(
            $messageChannelName,
            $endpointId,
            $requestChannelName,
            $connectionReferenceName,
        );
    }

    public static function createWith(string $endpointId, string $topicName, ?string $requestChannelName, string $connectionReferenceName = KafkaConnectionFactory::class, ?KafkaTopicConfiguration $topicConfig = null): self
    {
        return new self($topicName, $endpointId, $requestChannelName, $connectionReferenceName, $topicConfig);
    }

    public function createInboundChannelAdapter(ChannelResolver $channelResolver, ReferenceSearchService $referenceSearchService, PollingMetadata $pollingMetadata): KafkaInboundChannelAdapter
    {
        /** @var KafkaConnectionFactory $connectionFactory */
        $connectionFactory = $referenceSearchService->get($this->connectionReferenceName);
        /** @var ConversionService $conversionService */
        $conversionService = $referenceSearchService->get(ConversionService::REFERENCE_NAME);

        $headerMapper = DefaultHeaderMapper::createWith($this->headerMapper, [], $conversionService);

        return new KafkaInboundChannelAdapter(
            $connectionFactory,
            $this->buildGatewayFor($referenceSearchService, $channelResolver, $pollingMetadata),
            $this->declareOnStartup,
            $this->messageChannelName,
            $this->receiveTimeoutInMilliseconds,
            new InboundMessageConverter($this->getEndpointId(), $this->acknowledgeMode, $headerMapper),
            $this->topicConfig
        );
    }
}
