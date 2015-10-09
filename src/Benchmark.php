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
 * Simple benchmarking.
 */
class Benchmark
{
    /**
     * Benchmarks.
     *
     * @var array
     */
    private static $benchmarks = [];

    /**
     * Marker template.
     *
     * @var array
     */
    private static $benchmark = [
        'start' => -1.0,
        'stop' => -1.0,
        'time' => -1.0,
        'memory' => -1,
    ];

    /**
     * Set a benchmark start point.
     *
     * @param string $name The benchmark name
     *
     * @throws BenchEx When benchmark already started
     */
    public static function start($name)
    {
        if (isset(self::$benchmarks[$name])) {
            $index = count(self::$benchmarks[$name]);
            if (self::$benchmarks[$name][$index]['time_stop'] === -1) {
                throw new BenchEx('benchmark "'.$name.'" already started');
            }
        } else {
            self::$benchmarks[$name] = [];
        }

        $mark = [
            'time_start' => (float) microtime(true),
            'time_stop' => -1,
            'memory_start' => self::memory_usage(),
            'memory_stop' => -1,
        ];

        self::$benchmarks[$name][] = $mark;
    }

    /**
     * Set a benchmark stop point.
     *
     * @param string $name The benchmark name
     *
     * @throws BenchEx When benchmark already stopped or no benchmark by given name
     */
    public static function stop($name)
    {
        if (isset(self::$benchmarks[$name])) {
            // Last marker
            $index = count(self::$benchmarks[$name]) - 1;
            if (self::$benchmarks[$name][$index]['time_stop'] !== -1) {
                throw new BenchEx('benchmark "'.$name.'" already stopped');
            }
        } else {
            throw new BenchEx('benchmark "'.$name.'" does not exist (stop)');
        }

        self::$benchmarks[$name][$index]['time_stop'] = (float) microtime(true);
        self::$benchmarks[$name][$index]['memory_stop'] = self::memory_usage();
    }

    /**
     * Delete benchmark by name.
     *
     * @param string $name The benchmark name
     *
     * @return bool
     */
    public static function delete($name)
    {
        if (isset(self::$benchmarks[$name])) {
            unset(self::$benchmarks[$name]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the elapsed time between a start and stop.
     *
     * @param string $name     The benchmark name
     * @param int    $decimals The of decimal places to count to
     *
     * @throws BenchEx When benchmark name does not exist
     *
     * @return array
     */
    public static function get($name, $decimals = 6)
    {
        if (!isset(self::$benchmarks[$name])) {
            throw new BenchEx('benchmark "'.$name.'" does not exist (get)');
        }

        try {
            // Stop the benchmark and ignore error if already stopped
            static::stop($name);
        } catch (BenchEx $e) {
        }

        $benchmark = static::$benchmark;

        $time = $memory = $executions = 0;
        $count = count(self::$benchmarks[$name]);

        for ($i = 0; $i < $count; ++$i) {
            if ($i === 0) {
                $benchmark['start'] = self::$benchmarks[$name][$i]['time_start'];
            }

            if ($i === $count - 1) {
                $benchmark['stop'] = self::$benchmarks[$name][$i]['time_stop'];
            }

            $time += self::$benchmarks[$name][$i]['time_stop'] - self::$benchmarks[$name][$i]['time_start'];
            $memory += self::$benchmarks[$name][$i]['memory_stop'] - self::$benchmarks[$name][$i]['memory_start'];
        }

        $benchmark['time'] = number_format($time, $decimals);
        $benchmark['memory'] = $memory;
        $benchmark['executions'] = $count;

        return $benchmark;
    }

    /**
     * Get all markers.
     *
     * @param int $decimals The of decimal places to count to
     *
     * @return array The array of benchmarks
     */
    public static function getAll($decimals = 6)
    {
        $benchmarks = [];

        foreach (self::$benchmarks as $benchmarkName => $benchmark) {
            $benchmarks[$benchmarkName] = self::get($benchmarkName, $decimals);
        }

        // Return the array
        return $benchmarks;
    }

    /**
     * Returns the current memory usage.
     *
     * @return int
     */
    private static function memory_usage()
    {
        return memory_get_usage(true);
    }
}
