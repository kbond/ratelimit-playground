<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CompoundLimiter extends RateLimiter
{
    private array $limiters;

    public function __construct(RateLimiter ...$limiters)
    {
        if (count($limiters) < 2) {
            throw new \InvalidArgumentException('At least 2 limiters are required');
        }

        $this->limiters = $limiters;
    }

    public function hit(): RateLimit
    {
        /** @var RateLimit[] $rateLimits */
        $rateLimits = [];

        /** @var RateLimitExceeded[] $exceptions */
        $exceptions = [];

        foreach ($this->limiters as $limiter) {
            try {
                $rateLimits[] = $limiter->hit();
            } catch (RateLimitExceeded $e) {
                $exceptions[] = $e;
            }
        }

        if (count($exceptions)) {
            // sort so longest reset time is first
            usort($exceptions, fn(RateLimitExceeded $a, RateLimitExceeded $b) => $b->resetsIn() <=> $a->resetsIn());

            throw $exceptions[0];
        }

        // sort so smallest remaining is first
        usort($rateLimits, fn(RateLimit $a, RateLimit $b) => $a->remaining() <=> $b->remaining());

        return $rateLimits[0];
    }

    public function reset(): void
    {
        foreach ($this->limiters as $limiter) {
            $limiter->reset();
        }
    }

    protected function key(): string
    {
        return '';
    }
}
