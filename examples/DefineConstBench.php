<?php

class LangsClass
{
    const LANG_SOME = 'VALUE';
}

define('LANG_SOME', 'VALUE');

$benchmarks = [];

$benchmarks['const'] = function ($iterations) {
    for ($i = 0; $i < $iterations; ++$i) {
        $const = 'LangsClass::LANG_SOME';

        if (defined($const) && constant($const) != '') {
            $langText = stripcslashes(constant($const));

            if (!$langText) {
                die('WTF? (0)');
            }
        } else {
            die('WTF? (10)');
        }
    }
};

$benchmarks['define'] = function ($iterations) {
    for ($i = 0; $i < $iterations; ++$i) {
        $const = 'LANG_SOME';

        if (defined($const) && constant($const) != '') {
            $langText = stripcslashes(constant($const));

            if (!$langText) {
                die('WTF? (01)');
            }
        } else {
            die('WTF? (11)');
        }
    }
};

return $benchmarks;
