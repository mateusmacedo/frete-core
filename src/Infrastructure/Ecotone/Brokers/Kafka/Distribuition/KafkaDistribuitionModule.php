<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Distribuition;

use Ecotone\AnnotationFinder\AnnotationFinder;
use Ecotone\Messaging\Attribute\ModuleAnnotation;
use Ecotone\Messaging\Channel\SimpleMessageChannelBuilder;
use Ecotone\Messaging\Config\Annotation\AnnotationModule;
use Ecotone\Messaging\Config\Annotation\ModuleConfiguration\{ExtensionObjectResolver, NoExternalConfigurationModule};
use Ecotone\Messaging\Config\{Configuration, ConfigurationException, ModuleReferenceSearchService, ServiceConfiguration};
use Ecotone\Messaging\Handler\Gateway\GatewayProxyBuilder;
use Ecotone\Messaging\Handler\Gateway\ParameterToMessageConverter\{GatewayHeaderBuilder, GatewayHeaderValueBuilder, GatewayHeadersBuilder, GatewayPayloadBuilder};
use Ecotone\Messaging\Handler\InterfaceToCallRegistry;
use Ecotone\Messaging\Handler\Transformer\TransformerBuilder;
use Ecotone\Messaging\MessageHeaders;
use Ecotone\Messaging\Support\Assert;
use Ecotone\Modelling\Config\DistributedGatewayModule;
use Ecotone\Modelling\{DistributedBus, DistributionEntrypoint};
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Configuration\KafkaMessagePublisherConfiguration;
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\{KafkaBackedMessageChannelBuilder, KafkaOutboundChannelAdapterBuilder};

#[ModuleAnnotation]
class KafkaDistribuitionModule extends NoExternalConfigurationModule implements AnnotationModule
{
    // @phpstan-ignore-next-line
    private array $distributedEventHandlers;
    // @phpstan-ignore-next-line
    private array $distributedCommandHandlers;

    public function __construct(array $distributedEventHandlers, array $distributedCommandHandlers)
    {
        $this->distributedEventHandlers = $distributedEventHandlers;
        $this->distributedCommandHandlers = $distributedCommandHandlers;
    }

    public static function create(AnnotationFinder $annotationFinder, InterfaceToCallRegistry $interfaceToCallRegistry): static
    {
        // @phpstan-ignore-next-line
        return new self(
            DistributedGatewayModule::getDistributedEventHandlerRoutingKeys($annotationFinder, $interfaceToCallRegistry),
            DistributedGatewayModule::getDistributedCommandHandlerRoutingKeys($annotationFinder, $interfaceToCallRegistry)
        );
    }

    public function prepare(Configuration $configuration, array $extensionObjects, ModuleReferenceSearchService $moduleReferenceSearchService, InterfaceToCallRegistry $interfaceToCallRegistry): void
    {
        $registeredReferences = [];
        $applicationConfiguration = ExtensionObjectResolver::resolveUnique(ServiceConfiguration::class, $extensionObjects, ServiceConfiguration::createWithDefaults());

        foreach ($extensionObjects as $distributedBusConfiguration) {
            if (!$distributedBusConfiguration instanceof KafkaDistribuitedBusConfiguration) {
                continue;
            }

            if (in_array($distributedBusConfiguration->getReferenceName(), $registeredReferences)) {
                throw ConfigurationException::create("Registering two publishers under same reference name {$distributedBusConfiguration->getReferenceName()}. You need to create publisher with specific reference using `createWithReferenceName`.");
            }

            if ($distributedBusConfiguration->isPublisher()) {
                Assert::isFalse(ServiceConfiguration::DEFAULT_SERVICE_NAME === $applicationConfiguration->getServiceName(), "Service name can't be default when using distribution. Set up correct Service Name");

                $registeredReferences[] = $distributedBusConfiguration->getReferenceName();
                $this->registerPublisher($distributedBusConfiguration, $applicationConfiguration, $configuration);
            }

            if ($distributedBusConfiguration->isConsumer()) {
                Assert::isFalse(ServiceConfiguration::DEFAULT_SERVICE_NAME === $applicationConfiguration->getServiceName(), "Service name can't be default when using distribution. Set up correct Service Name");

                $channelName = $applicationConfiguration->getServiceName();
                $configuration->registerMessageChannel(KafkaBackedMessageChannelBuilder::create($channelName, $distributedBusConfiguration->getConnectionReference()));
                $configuration->registerMessageHandler(
                    TransformerBuilder::createHeaderEnricher([
                        MessageHeaders::ROUTING_SLIP => DistributionEntrypoint::DISTRIBUTED_CHANNEL,
                    ])
                        ->withEndpointId($applicationConfiguration->getServiceName())
                        ->withInputChannelName($channelName)
                );
            }
        }
    }

    public function canHandle($extensionObject): bool
    {
        return
            $extensionObject instanceof KafkaDistribuitedBusConfiguration
            || $extensionObject instanceof ServiceConfiguration;
    }

    public function getModulePackageName(): string
    {
        return 'kafka';
    }

    private function registerPublisher(KafkaDistribuitedBusConfiguration|KafkaMessagePublisherConfiguration $messagePublisher, ServiceConfiguration $applicationConfiguration, Configuration $configuration): void
    {
        $mediaType = $messagePublisher->getOutputDefaultConversionMediaType() ? $messagePublisher->getOutputDefaultConversionMediaType() : $applicationConfiguration->getDefaultSerializationMediaType();

        $configuration
            ->registerGatewayBuilder(
                GatewayProxyBuilder::create($messagePublisher->getReferenceName(), DistributedBus::class, 'sendCommand', $messagePublisher->getReferenceName())
                    ->withParameterConverters(
                        [
                            GatewayPayloadBuilder::create('command'),
                            GatewayHeadersBuilder::create('metadata'),
                            GatewayHeaderBuilder::create('sourceMediaType', MessageHeaders::CONTENT_TYPE),
                            GatewayHeaderBuilder::create('routingKey', DistributionEntrypoint::DISTRIBUTED_ROUTING_KEY),
                            GatewayHeaderValueBuilder::create(DistributionEntrypoint::DISTRIBUTED_PAYLOAD_TYPE, 'command'),
                        ]
                    )
            )
            ->registerGatewayBuilder(
                GatewayProxyBuilder::create($messagePublisher->getReferenceName(), DistributedBus::class, 'convertAndSendCommand', $messagePublisher->getReferenceName())
                    ->withParameterConverters(
                        [
                            GatewayPayloadBuilder::create('command'),
                            GatewayHeadersBuilder::create('metadata'),
                            GatewayHeaderBuilder::create('routingKey', DistributionEntrypoint::DISTRIBUTED_ROUTING_KEY),
                            GatewayHeaderValueBuilder::create(DistributionEntrypoint::DISTRIBUTED_PAYLOAD_TYPE, 'command'),
                        ]
                    )
            )
            ->registerGatewayBuilder(
                GatewayProxyBuilder::create($messagePublisher->getReferenceName(), DistributedBus::class, 'publishEvent', $messagePublisher->getReferenceName())
                    ->withParameterConverters(
                        [
                            GatewayPayloadBuilder::create('event'),
                            GatewayHeadersBuilder::create('metadata'),
                            GatewayHeaderBuilder::create('sourceMediaType', MessageHeaders::CONTENT_TYPE),
                            GatewayHeaderBuilder::create('routingKey', DistributionEntrypoint::DISTRIBUTED_ROUTING_KEY),
                            GatewayHeaderValueBuilder::create(DistributionEntrypoint::DISTRIBUTED_PAYLOAD_TYPE, 'event'),
                        ]
                    )
            )
            ->registerGatewayBuilder(
                GatewayProxyBuilder::create($messagePublisher->getReferenceName(), DistributedBus::class, 'convertAndPublishEvent', $messagePublisher->getReferenceName())
                    ->withParameterConverters(
                        [
                            GatewayPayloadBuilder::create('event'),
                            GatewayHeadersBuilder::create('metadata'),
                            GatewayHeaderBuilder::create('routingKey', DistributionEntrypoint::DISTRIBUTED_ROUTING_KEY),
                            GatewayHeaderValueBuilder::create(DistributionEntrypoint::DISTRIBUTED_PAYLOAD_TYPE, 'event'),
                        ]
                    )
            )
            ->registerMessageHandler(
                KafkaOutboundChannelAdapterBuilder::create($messagePublisher->getQueueName(), $messagePublisher->getConnectionReference(), $messagePublisher->getmessageBrokerHeadersReferenceName(), $messagePublisher->getKafkaTopicConfiguration())
                    ->withEndpointId($messagePublisher->getReferenceName() . '.handler')
                    ->withInputChannelName($messagePublisher->getReferenceName())
                    ->withAutoDeclareOnSend($messagePublisher->isAutoDeclareOnSend())
                    ->withHeaderMapper($messagePublisher->getHeaderMapper())
                    ->withDefaultConversionMediaType($mediaType)
            )
            ->registerGatewayBuilder(
                GatewayProxyBuilder::create($messagePublisher->getReferenceName(), DistributedBus::class, 'sendMessage', $messagePublisher->getReferenceName())
                    ->withParameterConverters(
                        [
                            GatewayPayloadBuilder::create('payload'),
                            GatewayHeadersBuilder::create('metadata'),
                            GatewayHeaderBuilder::create('sourceMediaType', MessageHeaders::CONTENT_TYPE),
                            GatewayHeaderBuilder::create('targetChannelName', DistributionEntrypoint::DISTRIBUTED_ROUTING_KEY),
                            GatewayHeaderValueBuilder::create(DistributionEntrypoint::DISTRIBUTED_PAYLOAD_TYPE, 'message'),
                        ]
                    )
            )
            ->registerMessageChannel(SimpleMessageChannelBuilder::createDirectMessageChannel($messagePublisher->getReferenceName()));
    }
}
