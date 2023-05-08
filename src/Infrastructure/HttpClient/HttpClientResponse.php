<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\HttpClient;

class HttpClientResponse
{
    public function __construct(
        public readonly int|string $statusCode,
        public readonly mixed $data = null,
        public readonly object|array $headers = [],
        public readonly bool $isFailed = false
    ) {
    }
}
