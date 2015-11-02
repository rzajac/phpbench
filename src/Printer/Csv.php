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

/**
 * Csv printer.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Csv extends Printer
{
    /**
     * Parse benchmark summary and return it string.
     *
     * @return string
     */
    protected function summaryToStr()
    {
        $msg = "Name,Case,Execution Percent,Execution Seconds,Memory Percent,Memory Bytes,Operations Per Second\n";

        $format = '%s,%s,%s,%s,%s,%s,%s';

        foreach ($this->summary as $name => $summary) {
            $executionActual = number_format($summary['time'], 6);
            $executionPerc = number_format($summary['to_fastest'] * 100, 2);

            $memoryActual = $summary['memory'];
            $memoryPerc = number_format($summary['to_least_memory'] * 100, 2);
            $per_sec = $summary['per_sec'] === 'unknown' ? -1 : $summary['per_sec'];

            $msg .= sprintf($format, $this->benchmarkName, $name, $executionPerc, $executionActual, $memoryPerc, $memoryActual, $per_sec);
            $msg .= "\n";
        }

        return $msg;
    }
}
