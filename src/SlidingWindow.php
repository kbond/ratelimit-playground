<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SlidingWindow extends WindowRateLimiter
{
    public function hit(): RateLimit
    {
        // source: https://engagor.github.io/blog/2018/09/11/error-internal-rate-limit-reached/
        $script = "
            local token = KEYS[1]
            local now = tonumber(ARGV[1])
            local window = tonumber(ARGV[2])
            local limit = tonumber(ARGV[3])
            
            local clearBefore = now - window
            redis.call('ZREMRANGEBYSCORE', token, 0, clearBefore)
            
            local amount = redis.call('ZCARD', token)
            if amount < limit then
                redis.call('ZADD', token, now, now)
                amount = amount + 1
            else
                return -1
            end
            redis.call('EXPIRE', token, window)
            
            return limit - amount
        ";

        $remaining = $this->redis->eval($script, [$this->key, microtime(true), $this->duration, $this->limit], 1);

        if ($remaining < 0) {
            throw new RateLimitExceeded();
        }

        return new RateLimit($remaining);
    }
}
