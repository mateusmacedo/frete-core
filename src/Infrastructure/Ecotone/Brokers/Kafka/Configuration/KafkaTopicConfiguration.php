<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Ecotone\Brokers\Kafka\Configuration;

final class KafkaTopicConfiguration
{
	private ?int $publisherPartition = null;
	private ?string $publisherKey = null;

	private array $consumerPartitions = [];

	/**
	 * @return
	 */
	public function getpublisherPartition(): ?int
	{
		return $this->publisherPartition;
	}

	/**
	 * @param  $publisherPartition
	 * @return self
	 */
	public function setpublisherPartition(int $publisherPartition): self
	{
		$this->publisherPartition = $publisherPartition;
		return $this;
	}

	/**
	 * @return
	 */
	public function getPublisherKey(): ?string
	{
		return $this->publisherKey;
	}

	/**
	 * @param  $publisherKey
	 * @return self
	 */
	public function setpublisherKey(string $publisherKey): self
	{
		$this->publisherKey = $publisherKey;
		return $this;
	}

	/**
	 * @return
	 */
	public function getConsumerPartitions(): array
	{
		return $this->consumerPartitions;
	}

	/**
	 * @param  $consumerPartitions
	 * @return self
	 */
	public function setConsumerPartitions(array $consumerPartitions): self
	{
		$this->consumerPartitions = $consumerPartitions;
		return $this;
	}
}