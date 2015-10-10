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

use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;
use Kicaj\Bench\Printer\Csv;
use Kicaj\Bench\Printer\Text;
use SplFileInfo;

/**
 * Class for running benchmarks from command line.
 */
class CliBench
{
    /** Output as CSV */
    const OUTPUT_CSV = 'csv';

    /** Output as TXT */
    const OUTPUT_TXT = 'txt';

    /**
     * File or directory iterator.
     *
     * @var \Iterator
     */
    protected $it;

    /**
     * Output format.
     *
     * @var string
     */
    protected $outputFormat = self::OUTPUT_TXT;

    /**
     * Command line arguments spec.
     *
     * @var OptionCollection
     */
    protected $specs;

    /**
     * Command line arguments parser.
     * 
     * @var OptionParser
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param array $argv The command line arguments
     *
     * @throws BenchEx
     */
    public function __construct(array $argv)
    {
        $this->setSpecs();
        $this->parseArgs($argv);
    }

    /**
     * Describe command line options.
     *
     * @throws \Exception
     */
    protected function setSpecs()
    {
        $this->specs = new OptionCollection();
        $this->specs->add('o|output?', 'output format: txt, csv')
                    ->isa('string')
                    ->validValues(['csv', 'txt'])
                    ->defaultValue(self::OUTPUT_TXT);

        $this->specs->add('d|dir?', 'directory')
                    ->isa('string');

        $this->specs->add('f|file?', 'file')
                    ->isa('string');
    }

    /**
     * Parse and validate command line arguments.
     *
     * @param array $argv The command line arguments
     *
     * @throws BenchEx
     * @throws \Exception
     * @throws \GetOptionKit\Exception\InvalidOptionException
     * @throws \GetOptionKit\Exception\RequireValueException
     */
    protected function parseArgs(array $argv)
    {
        $this->parser = new OptionParser($this->specs);
        $result = $this->parser->parse($argv);

        $this->outputFormat = $result->offsetGet('output')->getValue();

        $hasDir = $result->has('dir');
        $hasFile = $result->has('file');

        if (!($hasDir || $hasFile)) {
            $msg = "PHPBench usage:\n";
            $msg .= $this->getHelp();
            throw new BenchEx($msg);
        }

        if ($hasDir) {
            $this->it = new \RecursiveDirectoryIterator($result->offsetGet('dir')->getValue());
        } else {
            $this->it = [new SplFileInfo($result->offsetGet('file')->getValue())];
        }
    }

    /**
     * Return command line help.
     *
     * @return string
     */
    public function getHelp()
    {
        return (new ConsoleOptionPrinter())->render($this->specs);
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
     * @return string The benchmark results.
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

            switch ($this->outputFormat) {
                case self::OUTPUT_TXT:
                    $printer = new Text($file->getFilename(), $bench->getSummary());
                    break;

                case self::OUTPUT_CSV:
                    $printer = new Csv($file->getFilename(), $bench->getSummary());
                    break;
            }

            /* @noinspection PhpUndefinedVariableInspection */
            $msg .= $printer."\n";
        }

        return $msg;
    }
}
