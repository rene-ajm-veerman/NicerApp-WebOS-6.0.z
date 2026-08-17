<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Utils;

final class Http2Parallel
{
    private Client $client;
    private array $requests = [];
    private int $concurrency;
    private ?callable $fulfilled = null;
    private ?callable $rejected = null;

    public function __construct(array $clientConfig = [], int $concurrency = 200)
    {
        $this->concurrency = $concurrency;

        // Force HTTP/2 + sane defaults
        $defaultConfig = [
            'http_version' => '2.0',           // <-- HTTP/2 multiplexing
            'connect_timeout' => 10,
            'timeout' => 30,
            'decode_content' => true,
            'verify' => true,
            'headers' => [
                'User-Agent' => 'Http2Parallel/1.0 (+https://yourapp.com)',
            ],
        ];

        // Create handler stack with retry middleware
        $stack = HandlerStack::create();
        $stack->push($this->retryMiddleware());

        $this->client = new Client($defaultConfig + $clientConfig + ['handler' => $stack]);
    }

    /**
     * Add a request (can be called many times)
     */
    public function addRequest(string $key, Request $request, $userData = null): void
    {
        $this->requests[$key] = ['request' => $request, 'userData' => $userData];
    }

    /**
     * Set callbacks
     */
    public function onFulfilled(callable $callback): self
    {
        $this->fulfilled = $callback;
        return $this;
    }

    public function onRejected(callable $callback): self
    {
        $this->rejected = $callback;
        return $this;
    }

    /**
     * Execute all queued requests
     */
    public function execute(): void
    {
        if (empty($this->requests)) {
            return;
        }

        $generator = function () {
            foreach ($this->requests as $key => $item) {
                yield $key => $item['request'];
            }
        };

        $pool = new Pool($this->client, $generator(), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $key) {
                $userData = $this->requests[$key]['userData'] ?? null;
                if ($this->fulfilled) {
                    ($this->fulfilled)($response, $key, $userData);
                }
            },
            'rejected' => function ($reason, $key) {
                $userData = $this->requests[$key]['userData'] ?? null;
                if ($this->rejected) {
                    ($this->rejected)($reason, $key, $userData);
                }
            },
        ]);

        $pool->promise()->wait();

        // Clean up for next batch
        $this->requests = [];
    }

    /**
     * Built-in exponential backoff + jitter retry (up to 3 retries)
     */
    private function retryMiddleware(): callable
    {
        return Middleware::retry(
            function ($retries, $request, $response = null, $exception = null) {
                if ($retries >= 3) {
                    return false;
                }

                // Retry on 5xx, 429, or connection errors
                if ($exception instanceof RequestException) {
                    return true;
                }

                if ($response && $response->getStatusCode() >= 500) {
                    return true;
                }

                if ($response && $response->getStatusCode() === 429) {
                    return true;
                }

                return false;
            },
            function ($retries) {
                // Exponential backoff + full jitter
                $delay = 2 ** $retries * 1000; // ms
                return random_int(0, $delay);
            }
        );
    }
}
