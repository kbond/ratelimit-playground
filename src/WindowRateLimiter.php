<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class WindowRateLimiter extends RateLimiter
{
    protected int $limit;
    protected int $duration;

    public function __construct(int $limit, int $duration)
    {
        parent::__construct();

        $this->limit = $limit;
        $this->duration = $duration;
    }
}
