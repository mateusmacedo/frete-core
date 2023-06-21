<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka;

use Ecotone\Enqueue\{CachedConnectionFactory, EnqueueOutboundChannelAdapterBuilder, HttpReconnectableConnectionFactory, OutboundMessageConverter};
use Ecotone\Messaging\Conversion\ConversionService;
use Ecotone\Messaging\Handler\{ChannelResolver, ReferenceSearchService};
use Ecotone\Messaging\MessageConverter\DefaultHeaderMapper;
use Enqueue\RdKafka\{RdKafkaConnectionFactory, RdKafkaTopic};
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Configuration\KafkaTopicConfiguration;
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Connection\KafkaConnectionFactory;
use Frete\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\DefaultMessageHeader;

class KafkaOutboundChannelAdapterBuilder extends EnqueueOutboundChannelAdapterBuilder
{
    private array $staticHeadersToAdd = [];

    private function __construct(private string $topicName, private string $connectionFactoryReferenceName, private string $messageBrokerHeadersReferenceName, private ?KafkaTopicConfiguration $topicConfig)
    {
        $this->initialize($connectionFactoryReferenceName);
    }

    public static function create(string $topicName, string $connectionFactoryReferenceName = KafkaConnectionFactory::class, string $messageBrokerHeadersReferenceName = DefaultMessageHeader::class, ?KafkaTopicConfiguration $topicConfig = null): self
    {
        return new self($topicName, $connectionFactoryReferenceName, $messageBrokerHeadersReferenceName, $topicConfig);
    }

    public function build(ChannelResolver $channelResolver, ReferenceSearchService $referenceSearchService): KafkaOutboundChannelAdapter
    {
        /** @var KafkaConnectionFactory $connectionFactory */
        $connectionFactory = $referenceSearchService->get($this->connectionFactoryReferenceName);
        /** @var ConversionService $conversionService */
        $conversionService = $referenceSearchService->get(ConversionService::REFERENCE_NAME);

        // call the headers HERE!
        $messageBrokerHeadersReferenceName = new ($this->messageBrokerHeadersReferenceName)();

        $this->topicConfig = $this->topicConfig ?? new KafkaTopicConfiguration();
        $headerMapper = DefaultHeaderMapper::createWith([], $this->headerMapper, $conversionService);
        return new KafkaOutboundChannelAdapter(
            CachedConnectionFactory::createFor(new HttpReconnectableConnectionFactory($connectionFactory)),
            $this->buildKafkaTopic($this->topicName, $this->topicConfig),
            $this->autoDeclare,
            new OutboundMessageConverter($headerMapper, $conversionService, $this->defaultConversionMediaType, $this->defaultDeliveryDelay, $this->defaultTimeToLive, $this->defaultPriority, $this->staticHeadersToAdd),
            $messageBrokerHeadersReferenceName
        );
    }

    public function withStaticHeadersToEnrich(array $headers): self
    {
        $this->staticHeadersToAdd = $headers;

        return $this;
    }

    private function buildKafkaTopic(string $topicName, KafkaTopicConfiguration $topicConfig): RdKafkaTopic
    {
        $kafkaTopic = new RdKafkaTopic($topicName);

        if (!is_null($topicConfig->getpublisherPartition())) {
            $kafkaTopic->setPartition($topicConfig->getpublisherPartition());
        }
        if (!is_null($topicConfig->getPublisherKey())) {
            $kafkaTopic->setKey($topicConfig->getPublisherKey());
        }

        return $kafkaTopic;
    }
}
