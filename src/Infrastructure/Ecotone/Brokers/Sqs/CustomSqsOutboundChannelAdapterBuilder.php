<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Sqs;

use Ecotone\Enqueue\CachedConnectionFactory;
use Ecotone\Enqueue\EnqueueOutboundChannelAdapterBuilder;
use Ecotone\Enqueue\HttpReconnectableConnectionFactory;
use Ecotone\Enqueue\OutboundMessageConverter;
use Ecotone\Messaging\Handler\ChannelResolver;
use Ecotone\Messaging\Handler\ReferenceSearchService;
use Ecotone\Messaging\Conversion\ConversionService;
use Ecotone\Messaging\MessageConverter\DefaultHeaderMapper;
use Enqueue\Sqs\SqsConnectionFactory;
use Frete\Core\Infrastructure\Ecotone\Brokers\MessageBrokerHeaders\DefaultMessageHeader;

class CustomSqsOutboundChannelAdapterBuilder extends EnqueueOutboundChannelAdapterBuilder
{
    private function __construct(private string $queueName, private string $connectionFactoryReferenceName, private string $messageBrokerHeadersReferenceName)
    {
        $this->initialize($connectionFactoryReferenceName);
    }

    public static function create(string $queueName, string $connectionFactoryReferenceName = SqsConnectionFactory::class, string $messageBrokerHeadersReferenceName = DefaultMessageHeader::class): self
    {
        return new self($queueName, $connectionFactoryReferenceName, $messageBrokerHeadersReferenceName);
    }

    public function build(ChannelResolver $channelResolver, ReferenceSearchService $referenceSearchService): CustomSqsOutboundChannelAdapter
    {
        /** @var SqsConnectionFactory $connectionFactory */
        $connectionFactory = $referenceSearchService->get($this->connectionFactoryReferenceName);
        /** @var ConversionService $conversionService */
        $conversionService = $referenceSearchService->get(ConversionService::REFERENCE_NAME);

        /* call the headers HERE! */
        $messageBrokerHeadersReferenceName = new ($this->messageBrokerHeadersReferenceName)();

        $headerMapper = DefaultHeaderMapper::createWith([], $this->headerMapper, $conversionService);
        return new CustomSqsOutboundChannelAdapter(
            CachedConnectionFactory::createFor(new HttpReconnectableConnectionFactory($connectionFactory)),
            $this->queueName,
            $this->autoDeclare,
            new OutboundMessageConverter($headerMapper, $conversionService, $this->defaultConversionMediaType, $this->defaultDeliveryDelay, $this->defaultTimeToLive, $this->defaultPriority, []),
            $messageBrokerHeadersReferenceName
        );
    }
}
