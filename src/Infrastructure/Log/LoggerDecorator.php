<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Log;

use Attribute;
use Frete\Core\Shared\Decorator;
use Psr\Log\LoggerInterface;

#[Attribute(Attribute::TARGET_METHOD)]
class LoggerDecorator implements Decorator
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function execute(callable $action, ?array $arguments = [])
    {
        $this->logger->info('Method called with parameters: ' . json_encode($arguments));
        $result = $action(...$arguments);
        $this->logger->info('Method result: ' . json_encode($result));

        return $result;
    }
}
