<?php

$exampleArr = array('value1' => 1, 'value2' => 2, 'value3' => 3, 'value4' => 4, 'value5' => 5, 'value6' => 6, 'value7' => 7, 'value8' => 8);

$benchmarks = [];

$benchmarks['isset'] = function ($iterations) use ($exampleArr) {
    for ($i = 0; $i < $iterations; ++$i) {
        if (isset($exampleArr['value4'])) {
            // set
        } else {
            // not set
        }
    }
};

$benchmarks['array_key_exists'] = function ($iterations) use ($exampleArr) {
    for ($i = 0; $i < $iterations; ++$i) {
        if (array_key_exists('value4', $exampleArr)) {
            // exists
        } else {
            // does not exists
        }
    }
};

$benchmarks['empty'] = function ($iterations) use ($exampleArr) {
    for ($i = 0; $i < $iterations; ++$i) {
        if (empty($exampleArr['value4'])) {
            // empty
        } else {
            // not empty
        }
    }
};

return $benchmarks;
