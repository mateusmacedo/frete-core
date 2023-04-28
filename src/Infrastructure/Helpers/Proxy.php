<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Helpers;

use Frete\Core\Domain\Validators\ValidatorDecorator;
use Frete\Core\Infrastructure\Cache\CacheDecorator;
use Frete\Core\Infrastructure\Log\LoggerDecorator;
use Frete\Core\Infrastructure\Resilience\RetryDecorator;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use ReflectionMethod;

class Proxy
{
    public function __construct(
        private object $target,
        private ?LoggerInterface $logger = null,
        private ?CacheItemPoolInterface $cachePool = null
    ) {
    }

    public function __call(string $name, array $arguments)
    {
        $reflectionMethod = new ReflectionMethod($this->target, $name);

        // LoggerDecorator
        $loggerAttributes = $reflectionMethod->getAttributes(LoggerDecorator::class);
        $logDecorator = !empty($loggerAttributes) && $this->logger ? new LoggerDecorator($this->logger) : null;

        // CacheDecorator
        $cacheAttributes = $reflectionMethod->getAttributes(CacheDecorator::class);
        $cacheDecorator = !empty($cacheAttributes) && $this->cachePool ? $cacheAttributes[0]->newInstanceArgs([$this->cachePool]) : null;

        // RetryDecorator
        $retryAttributes = $reflectionMethod->getAttributes(RetryDecorator::class);
        $retryDecorator = !empty($retryAttributes) ? $retryAttributes[0]->newInstance() : null;

        // ValidationDecorator
        $parameters = $reflectionMethod->getParameters();
        $validationDecorators = [];
        foreach ($parameters as $index => $parameter) {
            $validationAttributes = $parameter->getAttributes(ValidatorDecorator::class);
            if (!empty($validationAttributes)) {
                $validationAttribute = $validationAttributes[0]->newInstance();
                $validationDecorators[] = new ValidatorDecorator($validationAttribute->getValidator(), $index);
            }
        }

        $action = function () use ($reflectionMethod, $arguments) {
            return $reflectionMethod->invoke($this->target, ...$arguments);
        };

        // Apply ValidationDecorator(s)
        foreach ($validationDecorators as $validationDecorator) {
            $action = function () use ($validationDecorator, $action, $arguments) {
                return $validationDecorator->execute($action, $arguments);
            };
        }

        // Apply RetryDecorator
        if ($retryDecorator) {
            $action = function () use ($retryDecorator, $action, $arguments) {
                return $retryDecorator->execute($action, $arguments);
            };
        }

        // Apply CacheDecorator
        if ($cacheDecorator) {
            $action = function () use ($cacheDecorator, $action, $arguments) {
                return $cacheDecorator->execute($action, $arguments);
            };
        }

        // Apply LoggerDecorator
        if ($logDecorator) {
            $action = function () use ($logDecorator, $action, $arguments) {
                return $logDecorator->execute($action, $arguments);
            };
        }

        return $action();
    }
}
