<?php

$date_time = '2013-10-14 08:55:00';

$benchmarks = [];

$benchmarks['str_replace'] = function ($iterations) use ($date_time) {
    for ($i = 0; $i < $iterations; ++$i) {
        $date = str_replace('-', '', $date_time);
        $date = str_replace(':', '', $date);
        $date_int = (int) str_replace(' ', '', $date);
    }
};

$benchmarks['str_replace_array'] = function ($iterations) use ($date_time) {
    for ($i = 0; $i < $iterations; ++$i) {
        $date = (int) str_replace(array(':', '-', ' '), '', $date_time);
    }
};

$benchmarks['b_substr'] = function ($iterations) use ($date_time) {
    for ($i = 0; $i < $iterations; ++$i) {
        $_date = substr($date_time, 0, 4);
        $_date .= substr($date_time, 5, 2);
        $_date .= substr($date_time, 8, 2);

        $_date .= substr($date_time, 11, 2);
        $_date .= substr($date_time, 14, 2);
        $_date .= substr($date_time, 17, 2);
        $_date = (int) $_date;
    }
};

$benchmarks['b_preg'] = function ($iterations) use ($date_time) {
    for ($i = 0; $i < $iterations; ++$i) {
        $_date = (int) preg_replace('/[^0-9]/', '', $date_time);
    }
};

return $benchmarks;
