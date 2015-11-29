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
 * Abstract benchmark summary printer.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
abstract class Printer
{
    /**
     * Benchmark summary.
     *
     * @var array
     */
    protected $summary;

    /**
     * The benchmark name.
     *
     * @var string
     */
    protected $benchmarkName;

    /**
     * Constructor.
     *
     * @param string $benchmarkName The benchmark name
     * @param array  $summary       The benchmark summary
     */
    public function __construct($benchmarkName, array $summary)
    {
        $this->benchmarkName = $benchmarkName;
        $this->summary = $summary;
    }

    /**
     * Parse benchmark summary and return it as a string.
     *
     * @return string[]
     */
    abstract public function summaryToStr();
}
