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

/**
 * Benchmarking class.
 */
class Bench
{
    /**
     * The number of iterations.
     *
     * @var int
     */
    protected $iterations;

    /**
     * Benchmarks.
     *
     * @var callable[]
     */
    protected $benchmarks = [];

    /**
     * The timer.
     *
     * @var Timer
     */
    protected $timer;

    /**
     * The benchmark summary.
     *
     * @var array
     */
    protected $summary = [];

    /**
     * The longest benchmark name.
     *
     * Used to align results in summary.
     *
     * @var int
     */
    protected $longestName = 0;

    /**
     * Constructor.
     *
     * @param int $iterations The number of iterations
     */
    private function __construct($iterations)
    {
        $this->iterations = $iterations;
        $this->timer = Timer::make();
    }

    /**
     * Make.
     *
     * @param int $iterations The number of iterations
     *
     * @return Bench
     */
    public static function make($iterations)
    {
        return new static($iterations);
    }

    /**
     * @param string   $name
     * @param callable $callback
     *
     * @return Bench
     *
     * @throws Exception
     */
    public function addBenchmark($name, callable $callback)
    {
        if (isset($this->benchmarks[$name])) {
            throw new Exception('benchmark "'.$name.'" already exists');
        }

        $this->benchmarks[$name] = $callback;

        return $this;
    }

    /**
     * Run benchmark.
     *
     * @throws BenchEx
     *
     * @return Bench
     */
    public function run()
    {
        foreach ($this->benchmarks as $name => $benchmark) {
            $this->timer->start($name);
            $benchmark($this->iterations);
            $this->timer->stop($name);
        }
        $this->summary();

        return $this;
    }

    /**
     * Create benchmark summary.
     *
     * @throws BenchEx
     */
    protected function summary()
    {
        $fastest = array('name' => '', 'value' => PHP_INT_MAX);
        $leastMemory = array('name' => '', 'value' => PHP_INT_MAX);

        $summary = $this->timer->getAll();
        $ourSummary = [];

        // Find fastest and least memory consuming one
        foreach ($summary as $name => $values) {
            $summary[$name]['time'] = $values['time'];

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
            $ourSummary[$name]['time'] = $value['time'];
            $ourSummary[$name]['memory'] = $value['memory'];
            $ourSummary[$name]['to_fastest'] = $value['time'] / $fastest['value'];
            $ourSummary[$name]['to_least_memory'] = $value['memory'] / $leastMemory['value'];
            $ourSummary[$name]['per_sec'] = ceil($this->iterations / $value['time']);
        }

        $this->summary = $ourSummary;
        uasort($this->summary, array($this, 'cmp'));
    }

    /**
     * Return printable summary.
     *
     * @return string
     */
    public function printSummary()
    {
        $msg = '';
        $format = 'Benchmark %s: execution: %s %% (%s sec), memory: %s %% (%s B), speed: %s /sec';

        foreach ($this->summary as $name => $summary) {
            $executionActual = number_format($summary['time'], 6);
            $executionPerc = number_format($summary['to_fastest'] * 100, 2);

            $memoryActual = $summary['memory'];
            $memoryPerc = number_format($summary['to_least_memory'] * 100, 2);

            $name = str_pad($name, $this->longestName, ' ', STR_PAD_LEFT);

            $name = Colors::getColoredString($name, 'blue');

            $per_sec = $summary['per_sec'] === 'unknown' ? 'unknown' : number_format($summary['per_sec'], 0, '.', ' ');

            $msg .= sprintf($format, $name, $executionPerc, $executionActual, $memoryPerc, $memoryActual, $per_sec);
            $msg .= "\n";
        }

        return $msg;
    }

    /**
     * Compare summaries.
     *
     * @param array $a The summary array
     * @param array $b The summary array
     *
     * @return int
     */
    public function cmp($a, $b)
    {
        if ($a['time'] == $b['time']) {
            return 0;
        }

        return ($a['time'] < $b['time']) ? -1 : 1;
    }
}
