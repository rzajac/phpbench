<?php

use Kicaj\Bench\Bench;

require __DIR__ . '/../vendor/autoload.php';

$exampleArr = ['value1' => 1, 'value2' => 2];

$benchmark = function ($iterations) use ($exampleArr) {
    for ($i = 0; $i < $iterations; ++$i) {
        if (isset($exampleArr['value1'])) {
            // set
        } else {
            // not set
        }
    }
};

$summary = Bench::make(10000)
                ->addBenchmark('isset', $benchmark)
                ->run()
                ->getSummary();

var_dump($summary);

/*

 array(1) {
  ["isset"]=>
  array(5) {
    ["time"]=>
    string(8) "0.000369"
    ["memory"]=>
    int(696)
    ["to_fastest"]=>
    float(1)
    ["to_least_memory"]=>
    int(1)
    ["per_sec"]=>
    float(27100272)
  }
}

*/



