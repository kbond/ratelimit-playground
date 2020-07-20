<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RateLimit
{
    public int $limit;
    public int $count;
    public int $resetsIn;

    public function __construct(int $limit, int $count, int $resetsIn)
    {
        $this->limit = $limit;
        $this->count = $count;
        $this->resetsIn = $resetsIn;
    }

    public function isExceeded(): bool
    {
        return $this->count > $this->limit;
    }

    public function remaining(): int
    {
        return max(0, $this->limit - $this->count);
    }
}
