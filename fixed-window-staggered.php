<?php

use RateLimit\Demo;
use RateLimit\FixedWindow;

require __DIR__.'/vendor/autoload.php';

$demo = new Demo(new FixedWindow(5, 10));

echo "# Fixed Window (staggered) Demo\n\n";
echo "Staggering hits in first window...\n";

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
