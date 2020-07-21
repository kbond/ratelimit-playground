<?php

use RateLimit\LeakyBucketLimiter;
use RateLimit\TokenBucketLimiter;

require __DIR__.'/vendor/autoload.php';

$limiters = [
    new LeakyBucketLimiter(5, 1, 1), // leak at 1 drip/sec
    new TokenBucketLimiter(5, 1, 1), // fill at 1 token/sec
];

foreach ($limiters as $limiter) {
    $limiter->reset();
}

$burstDetector = [];

$hitAll = function(int $num) use ($limiters, &$burstDetector) {
    foreach ($limiters as $key => $limiter) {
        if (!$limiter->hit()) {
            continue;
        }

        $burst = ($burstDetector[$key] ?? 0) === time();
        $burstDetector[$key] = time();

        echo sprintf("%s\t%s\t%s\t%s\n",
            $num,
            (new ReflectionClass($limiter))->getShortName(),
            date('H:i:s'),
            $burst ? '(burst)' : ''
        );
    }
};

while (true) {
    $hitAll(1);
    $hitAll(2);
    $hitAll(3);
    $hitAll(4);
    $hitAll(5);
    $hitAll(6);
    sleep(1);

    $hitAll(7);
    $hitAll(8);
    $hitAll(9);

    sleep(2);
    $hitAll(10);
    $hitAll(11);
    $hitAll(12);
}
