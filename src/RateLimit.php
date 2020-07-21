<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RateLimit
{
    private int $remaining;

    public function __construct(int $remaining)
    {
        $this->remaining = max(0, $remaining);
    }

    public function remaining(): int
    {
        return $this->remaining;
    }
}
