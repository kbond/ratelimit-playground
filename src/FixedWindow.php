<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FixedWindow extends RateLimiter
{
    public function hit(int $limit, int $duration): RateLimit
    {
        $count = $this->redis->hIncrBy($this->key, 'count', 1);

        if (1 === $count) {
            $this->redis->hSet($this->key, 'end', time() + $duration);
            $this->redis->expire($this->key, $duration);
        }

        return new RateLimit($limit, $count, $this->redis->hGet($this->key, 'end') - time());
    }
}
