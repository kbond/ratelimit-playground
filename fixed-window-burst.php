<?php

use RateLimit\Demo;
use RateLimit\FixedWindow;

require __DIR__.'/vendor/autoload.php';

$demo = new Demo(new FixedWindow(5, 10));

echo "# Fixed Window (burst) Demo\n\n";
echo "Saturate limiter...\n";

while (true) {
    $demo->acquire();
}
