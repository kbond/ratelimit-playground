<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RateLimit
{
    private int $remaining;
    private int $resetsIn;
    private int $limit;

    public function __construct(int $remaining, int $resetsIn, int $limit)
    {
        $this->remaining = max(0, $remaining);
        $this->resetsIn = max(0, $resetsIn);
        $this->limit = $limit;
    }

    public function remaining(): int
    {
        return $this->remaining;
    }

    public function resetsIn(): int
    {
        return $this->resetsIn;
    }

    public function limit(): int
    {
        return $this->limit;
    }
}
