<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TokenBucket extends RateLimiter
{
    private int $burst;
    private float $fillRate;

    /**
     * @param float $fillRate Tokens/Second
     */
    public function __construct(int $burst, float $fillRate)
    {
        parent::__construct();

        $this->burst = $burst;
        $this->fillRate = $fillRate;
    }

    public function hit(): RateLimit
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

            return new RateLimit($tokens);
        }

        throw new RateLimitExceeded();
    }

    private function timeToFill(int $tokens): int
    {
        return ceil(($this->burst - $tokens) / $this->fillRate);
    }
}
