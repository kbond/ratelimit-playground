<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RateLimit
{
    private int $remaining;
    private int $resetsIn;

    public function __construct(int $remaining, int $resetsIn)
    {
        $this->remaining = max(0, $remaining);
        $this->resetsIn = max(0, $resetsIn);
    }

    public function remaining(): int
    {
        return $this->remaining;
    }

    public function resetsIn(): int
    {
        return $this->resetsIn;
    }
}
