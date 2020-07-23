<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RateLimitExceeded extends \RuntimeException
{
    private RateLimit $rateLimit;

    public function __construct(RateLimit $rateLimit)
    {
        parent::__construct();

        $this->rateLimit = $rateLimit;
    }

    public function remaining(): int
    {
        return $this->rateLimit->remaining();
    }

    public function resetsIn(): int
    {
        return $this->rateLimit->resetsIn();
    }

    public function limit(): int
    {
        return $this->rateLimit->limit();
    }
}
