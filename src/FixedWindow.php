<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FixedWindow extends WindowRateLimiter
{
    public function hit(): RateLimit
    {
        $now = time();
        $count = $this->redis->hIncrBy($this->key, 'count', 1);

        if (1 === $count) {
            $this->redis->hSet($this->key, 'end', $now + $this->duration);

            $this->redis->expire($this->key, $this->duration);
        }

        $resetsIn = $this->redis->hGet($this->key, 'end') - $now;

        if ($count > $this->limit) {
            throw new RateLimitExceeded($resetsIn);
        }

        return new RateLimit($this->limit - $count, $resetsIn);
    }
}
