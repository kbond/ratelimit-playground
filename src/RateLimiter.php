<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class RateLimiter
{
    protected $redis;
    protected $key;

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
        $this->key = strtolower((new \ReflectionClass(static::class))->getShortName());
    }

    abstract public function hit(): bool;

    public function reset(): void
    {
        $this->redis->del($this->key);
    }
}
