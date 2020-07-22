<?php

use RateLimit\Demo;
use RateLimit\TokenBucket;

require __DIR__.'/vendor/autoload.php';

$demo = new Demo(new TokenBucket(5, 1));

echo "# Token Bucket (staggered) Demo\n\n";
echo "Staggering first hits...\n";

$demo->hit();
sleep(1);
$demo->hit();
sleep(1);
$demo->hit();
sleep(1);
$demo->hit();
sleep(1);

echo "Saturate limiter...\n";

while (true) {
    $demo->acquire();
}
