<?php

use RateLimit\CompoundLimiter;
use RateLimit\Demo;
use RateLimit\FixedWindow;

require __DIR__.'/vendor/autoload.php';

$demo = new Demo(new CompoundLimiter(
    new FixedWindow(4, 1),
    new FixedWindow(8, 10),
    new FixedWindow(12, 30),
));

echo "# Compound Fixed Window Demo\n\n";
echo "Hit limiter 1/s...\n";

while (true) {
    $demo->hitAndOutputException();
    sleep(1);
}
