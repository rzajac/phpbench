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

/**
 * Benchmarking class.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
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
     * Array where keys are benchmark names and values
     * are summary arrays.
     *
     * Summary array has following key values:
     *
     * time - the time it took to run the benchmark,
     * memory - the memory difference between benchmark start and stop,
     * to_fastest - speed ratio in percents,
     * to_least_memory - memory ratio in percent,
     * per_sec - number of operations per second for given benchmark
     *
     * @var array
     */
    protected $summary = [];

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
     * @throws BenchEx
     *
     * @return Bench
     */
    public function addBenchmark($name, callable $callback)
    {
        if (isset($this->benchmarks[$name])) {
            throw new BenchEx('benchmark "'.$name.'" already exists');
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
        $this->createSummary();

        return $this;
    }

    /**
     * Get benchmark summary.
     *
     * @return array
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Create benchmark summary.
     *
     * @throws BenchEx
     */
    protected function createSummary()
    {
        $fastest = ['name' => '', 'value' => PHP_INT_MAX];
        $leastMemory = ['name' => '', 'value' => PHP_INT_MAX];

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
        }

        foreach ($summary as $name => $value) {
            $ourSummary[$name] = [
                'time' => $value['time'],
                'memory' => $value['memory'],
                'to_fastest' => $value['time'] / $fastest['value'],
                'to_least_memory' => $value['memory'] / $leastMemory['value'],
                'per_sec' => ceil($this->iterations / $value['time']),
            ];
        }

        $this->summary = $ourSummary;
        uasort($this->summary, [$this, 'cmp']);
    }

    /**
     * Compare summaries.
     *
     * @param array $a The summary array
     * @param array $b The summary array
     *
     * @return int
     */
    protected function cmp($a, $b)
    {
        if ($a['time'] == $b['time']) {
            return 0;
        }

        return ($a['time'] < $b['time']) ? -1 : 1;
    }
}
