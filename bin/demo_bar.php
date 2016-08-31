<?php
include __DIR__ . '/../src/Bar.php';

use Q\Cli\Bar;

$total = 10273;
$bar = new Bar($total, "Removing old data");
for($i = 0; $i < $total; $i++) {
    $bar->tick();
    usleep(rand(100, 10000));
}
$bar->finish();
