<?php

use RateLimit\Demo;
use RateLimit\SlidingWindow;

require __DIR__.'/vendor/autoload.php';

$demo = new Demo(new SlidingWindow(5, 10));

echo "# Sliding Window (burst) Demo\n\n";
echo "Saturate limiter...\n";

while (true) {
    $demo->acquire();
}
