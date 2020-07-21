<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class LeakyBucketLimiter extends RateLimiter
{
    private int $capacity;
    private float $emptyRate;

    public function __construct(int $capacity, int $tokens, int $seconds)
    {
        parent::__construct();

        $this->capacity = $capacity;
        $this->emptyRate = $tokens / $seconds;
    }

    public function hit(): bool
    {
        $now = microtime(true);

        // get bucket
        $bucket = $this->redis->hGetAll($this->key);

        // get tokens, set to 0 if empty
        $tokens = $bucket['tokens'] ?? 0;

        // get last modified, set to now if empty
        $modified = $bucket['modified'] ?? $now;

        // determine how many tokens to remove since last modified based on empty rate
        $tokens -= floor(($now - $modified) * $this->emptyRate);

        if ($tokens < $this->capacity) { // bucket still has capacity
            // increment count and set last modified
            $this->redis->hMSet($this->key, [
                'tokens' => ++$tokens,
                'modified' => $now
            ]);

            // expire after "time to empty" based on current count
            $this->redis->expire($this->key, $this->timeToEmpty($tokens));

            return true;
        }

        return false;
    }

    private function timeToEmpty(int $tokens): int
    {
        return ceil($tokens / $this->emptyRate);
    }
}
