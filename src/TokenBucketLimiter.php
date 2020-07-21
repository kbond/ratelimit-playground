<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TokenBucketLimiter extends RateLimiter
{
    private int $burst;
    private float $fillRate;

    public function __construct(int $burst, int $tokens, int $second)
    {
        parent::__construct();

        $this->burst = $burst;
        $this->fillRate = $tokens / $second;
    }

    public function hit(): bool
    {
        $now = microtime(true);

        // get bucket
        $bucket = $this->redis->hGetAll($this->key);

        // get tokens, fill with burst if new
        $tokens = $bucket['tokens'] ?? $this->burst;

        // get last modified, set to now if new
        $modified = $bucket['modified'] ?? $now;

        // determine how many tokens to add since last modified based on drip rate (to a max of burst)
        $tokens = min($this->burst, $tokens + floor(($now - $modified) * $this->fillRate));

        if ($tokens > 0) { // bucket has tokens available
            // decrement tokens and set last modified
            $this->redis->hMSet($this->key, [
                'tokens' => --$tokens,
                'modified' => $now,
            ]);

            // expire after "time to fill" based on current count
            $this->redis->expire($this->key, $this->timeToFill($tokens));

            return true;
        }

        return false;
    }

    private function timeToFill(int $tokens): int
    {
        return ceil(($this->burst - $tokens) / $this->fillRate);
    }
}
