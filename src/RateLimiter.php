<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class RateLimiter
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
    }

    /**
     * @throws RateLimitExceeded
     */
    abstract public function hit(): RateLimit;

    public function reset(): void
    {
        $this->redis->del($this->key());
    }

    abstract protected function key(): string;
}
