<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FixedWindow extends WindowRateLimiter
{
    public function hit(): bool
    {
        $count = $this->redis->hIncrBy($this->key, 'count', 1);

        if (1 === $count) {
            $this->redis->hSet($this->key, 'end', time() + $this->duration);
            $this->redis->expire($this->key, $this->duration);
        }

        return $count <= $this->limit;
    }
}
