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

use Iterator;
use Kicaj\Bench\Printer\Csv;
use Kicaj\Bench\Printer\Text;
use Kicaj\Tools\Cli\Interaction;
use Kicaj\Tools\Helper\Str;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Benchmarking CLI command.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class BenchCommand extends Command
{
    /** Output as CSV */
    const OUTPUT_CSV = 'csv';

    /** Output as TXT */
    const OUTPUT_TXT = 'txt';

    protected function configure()
    {
        $this->setName('bench')
             ->setDescription('Run benchmark')
             ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'output format: txt, csv.', 'txt')
             ->addOption('iterations', 'i', InputOption::VALUE_OPTIONAL, 'number of benchmarking iterations', 10000)
             ->addArgument('fileOrDir', InputArgument::OPTIONAL, 'file or directory with benchmarks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format     = $input->getOption('format');
        $fileOrDir       = $input->getArgument('fileOrDir');
        $iterations = (int)$input->getOption('iterations');

        if (!$fileOrDir) {
            throw new BenchEx('you must provide directory or file');
        }

        if (is_file($fileOrDir) === false && is_dir($fileOrDir) === false) {
            throw new BenchEx('expected file or directory: ' . $fileOrDir);
        }

        if (is_dir($fileOrDir)) {
            $iterator = new \RecursiveDirectoryIterator($fileOrDir);
        } else {
            $iterator = [new SplFileInfo($fileOrDir)];
        }

        $benchSummary = $this->runBench($iterator, $iterations, $format);

        // $this->getGitVersion($fileOrDir);

        $output->writeln($benchSummary);
    }

    /**
     * Run benchmarks.
     *
     * @param Iterator $iterator   The file iterator
     * @param int      $iterations The number of benchmarking iterations
     * @param string   $format     The output format. One of the self::OUTPUT_* constants
     *
     * @throws \Exception
     *
     * @return string
     */
    public function runBench($iterator, $iterations, $format)
    {
        $msg = '';
        $csvHeader = '';

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->getExtension() != 'php') {
                continue;
            }

            if (substr($file->getFilename(), -9) != 'Bench.php') {
                continue;
            }

            $bench = Bench::make($iterations);

            /* @noinspection PhpIncludeInspection */
            $benchmarks = require $file->getRealPath();

            foreach ($benchmarks as $bName => $b) {
                $bench->addBenchmark($bName, $b);
            }

            $bench->run();
            $benchSummary = $bench->getSummary();
            $fileName = $file->getFilename();

            switch ($format) {
                case self::OUTPUT_TXT:
                    $printer = new Text($fileName, $benchSummary);
                    $msg .= implode("\n", $printer->summaryToStr());
                    break;

                case self::OUTPUT_CSV:
                    $printer = new Csv($fileName, $benchSummary);
                    $arr = $printer->summaryToStr();
                    $csvHeader = array_shift($arr);
                    $msg .= implode("\n", $arr);
                    break;
            }
        }

        if ($format == self::OUTPUT_CSV) {
            $msg = $csvHeader.$msg;
        }

        return $msg;
    }

    private function getGitVersion($fileOrDir)
    {
        $newestVersion = '0.0.0';
        $currentDir = getcwd();
        $gitCommand = 'git tag';

        if (!Interaction::commandExist('git')) {
            return $newestVersion;
        }

        if (Str::endsWith($fileOrDir, 'php')) {
            $dir = dirname($fileOrDir);
        } else  {
            $dir = $fileOrDir;
        }

        $dir = realpath($dir);
        chdir($dir);

        exec($gitCommand, $output);

        foreach ($output as $version) {
            if (version_compare($newestVersion, $version) === -1) {
                $newestVersion = $version;
            }
        }

        chdir($currentDir);

        return $newestVersion;
    }
}
