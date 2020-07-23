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
        $key = $this->key();

        // get bucket
        $bucket = $this->redis->hGetAll($key);

        // get tokens, fill with burst if new
        $tokens = $bucket['tokens'] ?? $this->burst;

        // get last modified, set to now if new
        $modified = $bucket['modified'] ?? $now;

        // determine how many tokens to add since last modified based on drip rate (to a max of burst)
        $tokens = (int) min($this->burst, $tokens + floor(($now - $modified) * $this->fillRate));

        if ($tokens > 0) { // bucket has tokens available
            // decrement tokens and set last modified
            $this->redis->hMSet($key, [
                'tokens' => --$tokens,
                'modified' => $now,
            ]);

            $timeToFill = $this->timeToFill($tokens);

            // expire after "time to fill" based on current count
            $this->redis->expire($key, $timeToFill);

            // if bucket empty, set reset to when next token is available
            // otherwise, set reset to time to fill bucket
            $resetIn = 0 === $tokens ? ceil(1 / $this->fillRate) : $timeToFill;

            return new RateLimit($tokens, $resetIn, $this->burst);
        }

        // set reset to when next token will be available
        throw new RateLimitExceeded(new RateLimit($tokens, ceil(1 / $this->fillRate), $this->burst));
    }

    protected function key(): string
    {
        return strtolower((new \ReflectionClass(static::class))->getShortName()).$this->burst.$this->fillRate;
    }

    private function timeToFill(int $tokens): int
    {
        return ceil(($this->burst - $tokens) / $this->fillRate);
    }
}
