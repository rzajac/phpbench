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
namespace Kicaj\Bench;

use Exception;
use Kicaj\Tools\Cli\Colors;

class Bench
{
    protected $iterations;
    protected $benchmarks = [];
    protected $summary;
    protected $longestName = 0;

    private function __construct($iterations)
    {
        $this->iterations = $iterations;
    }

    public static function make($iterations)
    {
        return new static($iterations);
    }

    /**
     * @param $name
     * @param $callback
     *
     * @return Bench
     *
     * @throws Exception
     */
    public function addBenchmark($name, $callback)
    {
        if (isset($this->benchmarks[$name])) {
            throw new Exception('Benchmark '.$name.' already exists.');
        }

        $this->benchmarks[$name] = $callback;

        return $this;
    }

    public function run()
    {
        foreach ($this->benchmarks as $name => $benchmark) {
            Benchmark::start($name);
            $benchmark($this->iterations);
            Benchmark::stop($name);
        }
        $this->summary();

        return $this;
    }

    protected function summary()
    {
        $fastest = array('name' => '', 'value' => PHP_INT_MAX);
        $leastMemory = array('name' => '', 'value' => PHP_INT_MAX);

        $summary = Benchmark::get(true, 6);

        $ourSummary = array();

        // Find fastest and least memory consuming one
        foreach ($summary as $name => $values) {
            $summary[$name]['time'] = floatval($values['time']);

            if ($fastest['value'] > $values['time']) {
                $fastest['name'] = $name;
                $fastest['value'] = $values['time'];
            }

            if ($leastMemory['value'] > $values['memory']) {
                $leastMemory['name'] = $name;
                $leastMemory['value'] = $values['memory'];
            }

            $this->longestName = max($this->longestName, strlen($name));
        }

        foreach ($summary as $name => $value) {
            $ourSummary[$name] = array();
            $ourSummary[$name]['execution'] = $value['time'];
            $ourSummary[$name]['memory'] = $value['memory'];
            $ourSummary[$name]['to_fastest'] = $value['time'] / $fastest['value'];
            $ourSummary[$name]['to_least_memory'] = $value['memory'] / $leastMemory['value'];

            if ($value['executions'] !== 0) {
                $ourSummary[$name]['per_sec'] = ceil($value['executions'] / $value['time']);
            } else {
                $ourSummary[$name]['per_sec'] = 'unknown';
            }
        }

        $this->summary = $ourSummary;
        uasort($this->summary, array($this, 'cmp'));
    }

    public function printSummary()
    {
        $format = 'Benchmark %s: execution: %s %% (%s sec), memory: %s %% (%s B), speed: %s /sec';
        foreach ($this->summary as $name => $summary) {
            $execution_actual = number_format($summary['execution'], 6);
            $execution_perc = number_format($summary['to_fastest'] * 100, 2);

            $memory_actual = $summary['memory'];
            $memory_perc = number_format($summary['to_least_memory'] * 100, 2);

            $name = str_pad($name, $this->longestName, ' ', STR_PAD_LEFT);

            $name = Colors::getColoredString($name, 'blue');

            $per_sec = $summary['per_sec'] === 'unknown' ? 'unknown' : number_format($summary['per_sec'], 0, '.', ' ');

            $msg = sprintf($format, $name, $execution_perc, $execution_actual, $memory_perc, $memory_actual, $per_sec);
            echo $msg."\n";
        }
    }

    public function cmp($a, $b)
    {
        if ($a['execution'] == $b['execution']) {
            return 0;
        }

        return ($a['execution'] < $b['execution']) ? -1 : 1;
    }
}
