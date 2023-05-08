<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure\HttpClient;

use Illuminate\Support\Facades\Http;

class GuzzleHttpClient implements IHttpClient
{
    private $headers = [];

    public function get(string $url, array $params = []): HttpClientResponse
    {
        return $this->request('get', $url, $params);
    }

    public function post(string $url, array $body = []): HttpClientResponse
    {
        return $this->request('post', $url, $body);
    }

    public function put(string $url, array $body = []): HttpClientResponse
    {
        return $this->request('put', $url, $body);
    }

    public function delete(string $url, array $data = []): HttpClientResponse
    {
        return $this->request('delete', $url, $data);
    }

    public function setHeaders(array $headers = []): IHttpClient
    {
        $this->headers = $headers;
        return $this;
    }

    private function request(string $action, string $url, array $data = []): HttpClientResponse
    {
        $req = $this->client()->{$action}($url, $data);
        return new HttpClientResponse($req->status(), $req->json(), $req->headers(), $req->failed());
    }

    private function client()
    {
        return Http::baseUrl('')
            ->withHeaders($this->headers);
    }
}
