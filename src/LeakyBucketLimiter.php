<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class LeakyBucketLimiter extends RateLimiter
{
    private int $capacity;
    private float $dripRate;

    public function __construct(int $capacity, int $drips, int $seconds)
    {
        parent::__construct();

        $this->capacity = $capacity;
        $this->dripRate = $drips / $seconds;
    }

    public function hit(): bool
    {
        $now = microtime(true);

        // get counter
        $state = $this->redis->hGetAll($this->key);

        // get count, set to 0 if empty
        $count = $state['count'] ?? 0;

        // get last modified, set to now if empty
        $modified = $state['modified'] ?? $now;

        // determine how many "drips" to remove since last modified based on drip rate
        $count -= floor(($now - $modified) * $this->dripRate);

        if ($count < $this->capacity) { // bucket still has capacity
            // increment count and set last modified
            $this->redis->hMSet($this->key, [
                'count' => ++$count,
                'modified' => $now
            ]);

            // expire after "time to empty" based on current count
            $this->redis->expire($this->key, $this->timeToEmpty($count));

            return true;
        }

        return false;
    }

    private function timeToEmpty(int $count): int
    {
        return ceil($count / $this->dripRate);
    }
}
