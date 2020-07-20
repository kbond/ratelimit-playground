<?php

use RateLimit\FixedWindow;

require __DIR__.'/vendor/autoload.php';

$limiter = new FixedWindow();
$limiter->reset();

while (true) {
    $rateLimit = $limiter->hit(5, 10);

    if ($rateLimit->isExceeded()) {
        echo "Available in {$rateLimit->resetsIn} seconds, waiting...\n";
        sleep($rateLimit->resetsIn);
    } else {
        echo sprintf("%s (limit: %d, remaining:%d)\n", date('H:i:s'), $rateLimit->limit, $rateLimit->remaining());
    }
}
