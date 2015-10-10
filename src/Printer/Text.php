<?php

/**
 * Copyright 2015 Rafal Zajac <rzajac@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace Kicaj\Bench\Printer;

use Kicaj\Tools\Cli\Colors;

/**
 * Text printer.
 *
 * @package Kicaj\Bench\Printer
 */
class Text extends Printer
{
    /**
     * Parse benchmark summary and return it string.
     *
     * @return string
     */
    protected function summaryToStr()
    {
        $longestName = max(array_map('strlen', array_keys($this->summary)));

        $msg = $this->benchmarkName."\n";
        $format = ' Benchmark %s: execution: %s %% (%s sec), memory: %s %% (%s B), speed: %s /sec';

        foreach ($this->summary as $name => $summary) {
            $executionActual = number_format($summary['time'], 6);
            $executionPerc = number_format($summary['to_fastest'] * 100, 2);

            $memoryActual = $summary['memory'];
            $memoryPerc = number_format($summary['to_least_memory'] * 100, 2);

            $name = str_pad($name, $longestName, ' ', STR_PAD_LEFT);
            $name = Colors::getColoredString($name, 'blue');

            $per_sec = $summary['per_sec'] === 'unknown' ? 'unknown' : number_format($summary['per_sec'], 0, '.', ' ');

            $msg .= sprintf($format, $name, $executionPerc, $executionActual, $memoryPerc, $memoryActual, $per_sec);
            $msg .= "\n";
        }

        return $msg;
    }
}
