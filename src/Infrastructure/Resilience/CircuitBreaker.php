<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Resilience;

class CircuitBreaker
{
    private int $failureCount = 0;
    private int $maxFailures;
    private int $resetTimeout;

    public function __construct(int $maxFailures, int $resetTimeout)
    {
        $this->maxFailures = $maxFailures;
        $this->resetTimeout = $resetTimeout;
    }

    public function recordSuccess(): void
    {
        $this->failureCount = 0;
    }

    public function recordFailure(): void
    {
        ++$this->failureCount;
    }

    public function isOpen(): bool
    {
        return $this->failureCount >= $this->maxFailures;
    }

    public function isHalfOpen(): bool
    {
        return $this->failureCount >= $this->maxFailures && time() - $this->resetTimeout >= 0;
    }

    public function isClosed(): bool
    {
        return $this->failureCount < $this->maxFailures;
    }

    public function getFailureCount(): int
    {
        return $this->failureCount;
    }

    public function getMaxFailures(): int
    {
        return $this->maxFailures;
    }

    public function getResetTimeout(): int
    {
        return $this->resetTimeout;
    }
}
