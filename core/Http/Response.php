<?php

declare(strict_types=1);

namespace Core\Http;

// wraps response data, headers, and status code for sending to the browser.
final class Response
{
    public function __construct(
        private readonly string $body = '',
        private readonly int $statusCode = 200,
        private readonly array $headers = ['Content-Type' => 'text/html; charset=UTF-8']
    ) {
    }

    public static function redirect(string $location): self
    {
        return new self('', 302, ['Location' => $location]);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->body;
    }
}
