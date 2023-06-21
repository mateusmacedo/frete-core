<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Distribuition;

use Ecotone\AnnotationFinder\AnnotationFinder;
use Ecotone\Messaging\Attribute\ModuleAnnotation;
use Ecotone\Messaging\Config\Annotation\AnnotationModule;
use Ecotone\Messaging\Config\Annotation\ModuleConfiguration\NoExternalConfigurationModule;
use Ecotone\Messaging\Config\{Configuration, ModuleReferenceSearchService};
use Ecotone\Messaging\Handler\InterfaceToCallRegistry;
use Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\KafkaBackedMessageChannelBuilder;

#[ModuleAnnotation]
class KafkaDistribuitedModule extends NoExternalConfigurationModule implements AnnotationModule
{
    private KafkaDistribuitionModule $kafkaDistribuitionModule;

    private function __construct(KafkaDistribuitionModule $kafkaDistribuitionModule)
    {
        $this->kafkaDistribuitionModule = $kafkaDistribuitionModule;
    }

    public static function create(AnnotationFinder $annotationRegistrationService, InterfaceToCallRegistry $interfaceToCallRegistry): static
    {
        // @phpstan-ignore-next-line
        return new self(KafkaDistribuitionModule::create($annotationRegistrationService, $interfaceToCallRegistry));
    }

    public function prepare(Configuration $messagingConfiguration, array $extensionObjects, ModuleReferenceSearchService $moduleReferenceSearchService, InterfaceToCallRegistry $interfaceToCallRegistry): void
    {
        $this->kafkaDistribuitionModule->prepare($messagingConfiguration, $extensionObjects);
    }

    public function canHandle($extensionObject): bool
    {
        return
            $extensionObject instanceof KafkaBackedMessageChannelBuilder
            || $this->kafkaDistribuitionModule->canHandle($extensionObject);
    }

    public function getModuleExtensions(array $serviceExtensions): array
    {
        return [];
    }

    public function getRelatedReferences(): array
    {
        return [];
    }

    public function getModulePackageName(): string
    {
        return 'kafka';
    }
}
