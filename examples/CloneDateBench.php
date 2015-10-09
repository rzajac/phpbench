<?php

$date = new DateTime('now');

$benchmarks = [];

$benchmarks['new_date'] = function ($iterations) use ($date) {
    for ($i = 0; $i < $iterations; ++$i) {
        $d = new DateTime('now');
        $hour = $i % 12 + 1;
        $d->setTime($hour, $hour + 2, $hour + 4);
    }
};

$benchmarks['clone_date'] = function ($iterations) use ($date) {
    for ($i = 0; $i < $iterations; ++$i) {
        $d = clone $date;
        $hour = $i % 12 + 1;
        $d->setTime($hour, $hour + 2, $hour + 4);
    }
};

return $benchmarks;
