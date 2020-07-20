<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class RateLimiter
{
    protected $redis;
    protected $key;
    protected int $limit;
    protected int $duration;

    public function __construct(int $limit, int $duration)
    {
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
        $this->key = strtolower((new \ReflectionClass(static::class))->getShortName());
        $this->limit = $limit;
        $this->duration = $duration;
    }

    abstract public function hit(): bool;

    public function reset(): void
    {
        $this->redis->del($this->key);
    }
}
