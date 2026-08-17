<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

final class UltraParallel
{
    private Client $client;
    private array $requests = [];
    private array $results = ['success' => [], 'failed' => []];

    private ?callable $fulfilled = null;
    private ?callable $rejected = null;
    private ?ProgressBar $progressBar = null;

    private int $total = 0;
    private int $completed = 0;

    public function __construct(array $clientConfig = [], int $concurrency = 200)
    {
        $output = new ConsoleOutput();

        // === HTTP/3 + HTTP/2 fallback (Guzzle auto-negotiates best protocol) ===
        $defaultConfig = [
            'http_version' => '3.0', // Try HTTP/3 (QUIC) first → falls back to HTTP/2 → HTTP/1.1
            'connect_timeout' => 12,
            'timeout' => 30,
            'decode_content' => true,
            'verify' => true,
            'headers' => [
                'User-Agent' => 'UltraParallel/2.0 (HTTP/3+HTTP/2)',
            ],
        ];

        $stack = HandlerStack::create();
        $stack->push($this->retryMiddleware());
        $stack->push($this->progressMiddleware());

        $this->client = new Client($defaultConfig + $clientConfig + ['handler' => $stack]);
        $this->progressBar = new ProgressBar($output);
        $this->progressBar->setFormat(
            " %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%\n %message%"
        );
        $this->progressBar->setMessage('Starting...');
    }

    public function add(string $key, Request $request, $userData = null): self
    {
        $this->requests[$key] = ['request' => $request, 'userData' => $userData];
        $this->total++;
        return $this;
    }

    public function onFulfilled(?callable $callback): self
    {
        $this->fulfilled = $callback;
        return $this;
    }

    public function onRejected(?callable $callback): self
    {
        $this->rejected = $callback;
        return $this;
    }

    public function execute(): array
    {
        if ($this->total === 0) {
            return $this->results;
        }

        $this->progressBar->setMaxSteps($this->total);
        $this->progressBar->start();

        $generator = function () {
            foreach ($this->requests as $key => $item) {
                yield $key => $item['request'];
            }
        };

        $pool = new Pool($this->client, $generator(), [
            'concurrency' => 999, // HTTP/3 & HTTP/2 allow thousands of streams
            'fulfilled' => function ($response, $key) {
                $userData = $this->requests[$key]['userData'] ?? null;
                $this->results['success'][$key] = [
                    'response'  => $response,
                    'body'      => $response->getBody()->getContents(),
                    'status'    => $response->getStatusCode(),
                    'headers'   => $response->getHeaders(),
                    'userData'  => $userData,
                ];

                if ($this->fulfilled) {
                    ($this->fulfilled)($response, $key, $userData);
                }

                $this->advanceProgress("✓ {$key}");
            },
            'rejected' => function ($reason, $key) {
                $userData = $this->requests[$key]['userData'] ?? null;
                $this->results['failed'][$key] = [
                    'reason'   => $reason,
                    'message'  => $reason instanceof RequestException
                        ? $reason->getMessage()
                        : (string)$reason,
                    'userData' => $userData,
                ];

                if ($this->rejected) {
                    ($this->rejected)($reason, $key, $userData);
                }

                $this->advanceProgress("✗ {$key}");
            },
        ]);

        $pool->promise()->wait();

        $this->progressBar->finish();
        echo PHP_EOL . "All {$this->total} requests completed!" . PHP_EOL;

        return $this->results;
    }

    private function advanceProgress(string $message): void
    {
        $this->completed++;
        $this->progressBar->setMessage($message);
        $this->progressBar->advance();
    }

    private function retryMiddleware(): callable
    {
        return Middleware::retry(
            fn($retries, $request, $response, $exception) =>
                $retries < 3 && (
                    $exception instanceof RequestException ||
                    ($response && ($response->getStatusCode() >= 500 || $response->getStatusCode() === 429))
                ),
            fn($retries) => random_int(0, (int)(1000 * (2 ** $retries)))
        );
    }

    private function progressMiddleware(): callable
    {
        return Middleware::mapRequest(function ($request) {
            // Optional: attach trace ID or logging here
            return $request;
        });
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getSuccessCount(): int
    {
        return count($this->results['success']);
    }

    public function getFailedCount(): int
    {
        return count($this->results['failed']);
    }
}
?>
