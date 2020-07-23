<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Demo
{
    private RateLimiter $limiter;
    private int $burstDetector = 0;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
        $this->limiter->reset();
    }

    public function hit(): void
    {
        $now = time();
        $rateLimit = $this->limiter->hit();
        $isBurst = $this->burstDetector === $now;
        $this->burstDetector = $now;

        echo sprintf("%s\t%s\t\tremaining=%d; reset=%d; limit=%d\n",
            date('H:i:s'),
            $isBurst ? '(burst)' : '',
            $rateLimit->remaining(),
            $rateLimit->resetsIn(),
            $rateLimit->limit(),
        );
    }

    public function hitAndOutputException(): void
    {
        try {
            $this->hit();
        } catch (RateLimitExceeded $e) {
            echo sprintf("%s\t%s\tremaining=%d; reset=%d; limit=%d\n",
                date('H:i:s'),
                '(exceeded)',
                $e->remaining(),
                $e->resetsIn(),
                $e->limit(),
            );
        }
    }

    public function acquire(): void
    {
        try {
            $this->hit();
        } catch (RateLimitExceeded $e) {
            echo "Rate limit exceeded, waiting {$e->resetsIn()} seconds...\n";
            sleep($e->resetsIn());
            $this->hit();
        }
    }
}
