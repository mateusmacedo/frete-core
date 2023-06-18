<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\Cache;

use Attribute;
use Frete\Core\Shared\Decorator;
use Psr\Cache\CacheItemPoolInterface;

#[Attribute(Attribute::TARGET_METHOD)]
class CacheDecorator implements Decorator
{
    public function __construct(private CacheItemPoolInterface $cachePool, private int $ttl, private ?string $cacheKey = null)
    {
    }

    public function execute(callable $action, ?array $arguments = [])
    {
        $cacheKey = $this->cacheKey ?? md5(json_encode($arguments));
        $cacheItem = $this->cachePool->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $action(...$arguments);
        $cacheItem->set($result)->expiresAfter($this->ttl);
        $this->cachePool->save($cacheItem);

        return $result;
    }
}
