<?php

use RateLimit\FixedWindow;
use RateLimit\SlidingWindow;

require __DIR__.'/vendor/autoload.php';

$limiters = [
    new FixedWindow(5, 10),
    new SlidingWindow(5, 10),
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

        $burst = ($burstDetector[$key] ?? time()) === time();
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
    sleep(1);

    $hitAll(2);
    sleep(1);

    $hitAll(3);
    $hitAll(4);
    $hitAll(5);
    $hitAll(6);
    $hitAll(7);
    sleep(1);
}
