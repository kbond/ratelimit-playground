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
            local resetsAt = now + window
            
            -- clear old hits
            redis.call('ZREMRANGEBYSCORE', token, 0, clearBefore)
            
            -- get # of hits
            local amount = redis.call('ZCARD', token)
            
            if amount > 0 then
                resetsAt = redis.call('ZRANGE', token, 0, 0)[1] + window
            end
            
            if amount < limit then
                redis.call('ZADD', token, now, now)
                amount = amount + 1
            else
                return {-1, resetsAt}
            end
            
            redis.call('EXPIRE', token, window)
            
            return {limit - amount, resetsAt}
        ";

        [$remaining, $resetsAt] = $this->redis->eval($script, [$this->key, microtime(true), $this->duration, $this->limit], 1);

        $rateLimit = new RateLimit($remaining, $resetsAt - time(), $this->limit);

        if ($remaining < 0) {
            throw new RateLimitExceeded($rateLimit);
        }

        return $rateLimit;
    }
}
