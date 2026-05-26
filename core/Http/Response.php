<?php // Start Response class file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Http; // Namespace for HTTP classes.

final class Response // Represents the HTTP response sent to browser.
{
    /**
     * HTTP response object; stores body, status code, and headers.
     *
     * @param array<string, string> $headers
     */
    public function __construct( // Constructor stores response body/status/headers.
        private readonly string $body = '', // HTML/text body to print.
        private readonly int $statusCode = 200, // HTTP status code, default OK.
        private readonly array $headers = ['Content-Type' => 'text/html; charset=UTF-8'] // Response headers.
    ) {
    } // Constructor ends.

    public static function redirect(string $location): self // Build redirect response.
    {
        // Create redirect response; browser will go to another URL.
        return new self('', 302, ['Location' => $location]); // 302 tells browser to redirect.
    }

    public function send(): void // Send response to browser.
    {
        // Send status code first.
        http_response_code($this->statusCode); // Set HTTP response status.

        // Send all headers like Content-Type or Location.
        foreach ($this->headers as $name => $value) { // Loop each header pair.
            header("{$name}: {$value}"); // Send header line.
        } // End headers loop.

        // Print response body to browser.
        echo $this->body; // Output final body.
    }
} // End Response class.
