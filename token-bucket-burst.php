<?php

use RateLimit\Demo;
use RateLimit\TokenBucket;

require __DIR__.'/vendor/autoload.php';

$demo = new Demo(new TokenBucket(5, 1));

echo "# Token Bucket (burst) Demo\n\n";
echo "Saturate limiter...\n";

while (true) {
    $demo->acquire();
}
