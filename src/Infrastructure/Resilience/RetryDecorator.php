<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Resilience;

use Attribute;
use Frete\Shared\Decorator;
use Throwable;

#[Attribute(Attribute::TARGET_METHOD)]
class RetryDecorator implements Decorator
{
    public function __construct(private int $maxAttempts, private int $delay, private ?array $retryOnExceptions = null)
    {
    }

    public function execute(callable $action, ?array $arguments = [])
    {
        for ($attempt = 1; $attempt <= $this->maxAttempts; ++$attempt) {
            try {
                return $action(...$arguments);
            } catch (Throwable $exception) {
                if ($attempt === $this->maxAttempts || (null !== $this->retryOnExceptions && !in_array(get_class($exception), $this->retryOnExceptions, true))) {
                    throw $exception;
                }

                usleep(intval($this->delay * 1000));
            }
        }
    }
}
