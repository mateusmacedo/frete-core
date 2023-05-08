<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\HttpClient;

interface IHttpClient
{
    public function get(string $url, array $params = []): HttpClientResponse;

    public function post(string $url, array $body = []): HttpClientResponse;

    public function put(string $url, array $body = []): HttpClientResponse;

    public function delete(string $url, array $data = []): HttpClientResponse;

    public function setHeaders(array $headers = []): IHttpClient;
}
