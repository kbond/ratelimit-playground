<?php

namespace RateLimit;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RateLimitExceeded extends \RuntimeException
{
    private int $resetsIn;

    public function __construct(int $resetsIn)
    {
        parent::__construct();

        $this->resetsIn = $resetsIn;
    }

    public function resetsIn(): int
    {
        return $this->resetsIn;
    }
}
