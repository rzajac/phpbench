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

use Kicaj\Bench\Printer\Csv;
use SplFileInfo;

/**
 * Class for running benchmarks from command line.
 */
class CliBench
{
    /**
     * CLI arguments.
     *
     * @var array
     */
    protected $argv = [];

    /**
     * File or directory iterator.
     *
     * @var \Iterator
     */
    protected $it;

    /**
     * Constructor.
     *
     * @param array $argv The command line arguments
     *
     * @throws BenchEx
     */
    public function __construct(array $argv)
    {
        $this->argv = $argv;

        if (count($this->argv) != 2) {
            throw new BenchEx('please provide file or directory to bench');
        }

        if (is_dir($this->argv[1])) {
            $this->it = new \RecursiveDirectoryIterator($this->argv[1]);
        } else {
            $this->it = [new SplFileInfo($this->argv[1])];
        }
    }

    /**
     * Make.
     *
     * @param array $argv The command line arguments
     *
     * @return static
     */
    public static function make(array $argv)
    {
        return new static($argv);
    }

    /**
     * Run benchmarks.
     *
     * @throws \Exception
     */
    public function run()
    {
        $msg = '';

        /** @var SplFileInfo $file */
        foreach ($this->it as $file) {
            if ($file->getExtension() != 'php') {
                continue;
            }

            if (substr($file->getFilename(), -9) != 'Bench.php') {
                continue;
            }

            $bench = Bench::make(10000);

            /* @noinspection PhpIncludeInspection */
            $benchmarks = require $file->getRealPath();

            foreach ($benchmarks as $bName => $b) {
                $bench->addBenchmark($bName, $b);
            }

            $bench->run();

            $printer = new Csv($file->getFilename(), $bench->getSummary());

            $msg .= $printer."\n";
        }

        return $msg;
    }
}
