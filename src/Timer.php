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
 * Simple timer class.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Timer
{
    /**
     * Array of named timers.
     *
     * @var array
     */
    protected $timers = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Make.
     *
     * @return Timer
     */
    public static function make()
    {
        return new static();
    }

    /**
     * Set a timer start point.
     *
     * @param string $name The timer name.
     * @param array  $data The associative array with data for benchmarking purposes.
     *
     * @throws BenchEx When timer already started
     */
    public function start($name, array $data = [])
    {
        if (isset($this->timers[$name])) {
            // Index of the last timer
            $index = count($this->timers[$name]) - 1;
            if ($this->timers[$name][$index]['time_stop'] === -1) {
                throw new BenchEx('timer "'.$name.'" already started');
            }
        } else {
            $this->timers[$name] = [];
        }

        $mark = [
            'time_start' => (float) microtime(true),
            'time_stop' => -1,
            'memory_start' => $this->memory_usage(),
            'memory_stop' => -1,
            'data' => $data
        ];

        $this->timers[$name][] = $mark;
    }

    /**
     * Set a timer stop point.
     *
     * @param string $name The timer name.
     * @param array  $data The associative array with data for benchmarking purposes.
     *
     * @throws BenchEx
     */
    public function stop($name, array $data = [])
    {
        if (isset($this->timers[$name])) {
            // Index of the last timer
            $index = count($this->timers[$name]) - 1;
            if ($this->timers[$name][$index]['time_stop'] !== -1) {
                throw new BenchEx('timer "'.$name.'" already stopped');
            }
        } else {
            throw new BenchEx('timer "'.$name.'" does not exist (stop)');
        }

        $this->timers[$name][$index]['time_stop'] = (float) microtime(true);
        $this->timers[$name][$index]['memory_stop'] = $this->memory_usage();

        if ($data) {
            $this->timers[$name][$index]['data'] = $data;
            $this->timers[$name][$index]['data']['time'] = $this->timers[$name][$index]['time_stop'] - $this->timers[$name][$index]['time_start'];
        }
    }

    /**
     * Delete timer by name.
     *
     * @param string $name The timer name
     *
     * @return bool
     */
    public function delete($name)
    {
        if (isset($this->timers[$name])) {
            unset($this->timers[$name]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Clear all timers.
     *
     * @return Timer
     */
    public function clear()
    {
        $this->timers = [];

        return $this;
    }

    /**
     * Get the elapsed time between a start and stop.
     *
     * @param string $name     The timer name
     * @param int    $decimals The of decimal places to count to
     *
     * @throws BenchEx When timer name does not exist
     *
     * @return array
     */
    public function get($name, $decimals = 6)
    {
        if (!isset($this->timers[$name])) {
            throw new BenchEx('timer "'.$name.'" does not exist (get)');
        }

        try {
            // Stop the timer and ignore error if already stopped
            static::stop($name);
        } catch (BenchEx $e) {
        }

        $timer = [
            'start' => -1.0,
            'stop' => -1.0,
            'time' => -1.0,
            'memory' => -1,
            'data' => []
        ];

        $time = $memory = 0;
        $count = count($this->timers[$name]);

        for ($i = 0; $i < $count; ++$i) {
            $t = $this->timers[$name][$i];

            if ($i === 0) {
                $timer['start'] = $t['time_start'];
            }

            if ($i === $count - 1) {
                $timer['stop'] = $t['time_stop'];
            }

            if ($t['data']) {
                // Do not add empty data arrays
                $timer['data'][$i] = $t['data'];
            }

            $time += $t['time_stop'] - $t['time_start'];
            $memory += $t['memory_stop'] - $t['memory_start'];
        }

        $timer['time'] = number_format($time, $decimals);
        $timer['memory'] = $memory;
        $timer['executions'] = $count;

        return $timer;
    }

    /**
     * Get all timers.
     *
     * @param int $decimals The of decimal places to count to
     *
     * @return array The array of timers
     */
    public function getAll($decimals = 6)
    {
        $timers = [];

        foreach ($this->timers as $timerName => $timer) {
            $timers[$timerName] = $this->get($timerName, $decimals);
        }

        // Return the array
        return $timers;
    }

    /**
     * Returns the current memory usage.
     *
     * @return int
     */
    private function memory_usage()
    {
        return memory_get_usage();
    }
}
