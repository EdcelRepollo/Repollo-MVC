<?php

declare(strict_types=1);

namespace Core\Http;

// route object nga nagdala sa controller, action, ug route parameters.
final class Route
{
    public function __construct(
        public readonly string $controller,
        public readonly string $action,
        public readonly array $params = [],
    ) {}
}
