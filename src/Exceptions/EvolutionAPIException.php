<?php

namespace EvolutionAPI\Exceptions;

use Exception;
use Throwable;

class EvolutionAPIException extends Exception
{
    private array $context;

    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function addContext(string $key, $value): void
    {
        $this->context[$key] = $value;
    }

    public function __toString(): string
    {
        $result = parent::__toString();

        if (!empty($this->context)) {
            $result .= "\nContexto: " . json_encode($this->context, JSON_PRETTY_PRINT);
        }

        return $result;
    }
}