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
namespace Kicaj\Test\Bench;

use Kicaj\Bench\Bench;

/**
 * BenchTest.
 *
 * @coversDefaultClass \Kicaj\Bench\Bench
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class BenchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bench
     */
    protected $bench;

    /**
     * @var TestBench
     */
    protected $spy;

    protected function setUp()
    {
        $this->bench = Bench::make(42);
        $this->spy = new TestBench;
    }

    /**
     * @covers ::__construct
     * @covers ::make
     */
    public function test___construct()
    {
        $bench = Bench::make(123);

        $this->assertInstanceOf('\Kicaj\Bench\Bench', $bench);
    }

    /**
     * @covers ::__construct
     * @covers ::addBenchmark
     * @covers ::run
     */
    public function test___construct_iterations()
    {
        $this->bench->addBenchmark('benchName', [$this->spy, 'bench1'])->run();
        $this->assertSame(42, $this->spy->getIterations());
    }

    /**
     * @expectedException \Kicaj\Bench\BenchEx
     * @expectedExceptionMessage benchmark "benchName" already exists
     *
     * @covers ::addBenchmark
     */
    public function test_addBenchmark_alreadyExisting()
    {
        $this->bench->addBenchmark('benchName', [$this->spy, 'bench1']);
        $this->bench->addBenchmark('benchName', [$this->spy, 'bench1']);
    }

    /**
     * @covers ::getSummary
     * @covers ::createSummary
     * @covers ::cmp
     */
    public function test_getSummary()
    {
        $gotSummary = $this->bench->getSummary();
        $this->assertSame([], $gotSummary);

        $this->bench->addBenchmark('benchName1', [$this->spy, 'bench1']);
        $this->bench->addBenchmark('benchName2', [$this->spy, 'bench1']);

        $gotSummary = $this->bench->getSummary();
        $this->assertSame([], $gotSummary);

        $this->bench->run();

        $gotSummary = $this->bench->getSummary();
        $this->assertSame(['benchName2', 'benchName1'], array_keys($gotSummary));
    }

    /**
     * @covers ::getSummary
     * @covers ::createSummary
     * @covers ::cmp
     */
    public function test_getSummary_structure()
    {
        $this->bench->addBenchmark('benchName1', [$this->spy, 'bench1'])->run();

        $gotSummary = $this->bench->getSummary();

        $this->assertSame(['benchName1'], array_keys($gotSummary));

        $gotSummary = $gotSummary['benchName1'];

        $this->assertArrayHasKey('time', $gotSummary);
        $this->assertArrayHasKey('memory', $gotSummary);
        $this->assertArrayHasKey('to_fastest', $gotSummary);
        $this->assertArrayHasKey('to_least_memory', $gotSummary);
        $this->assertArrayHasKey('per_sec', $gotSummary);

        $this->assertTrue($gotSummary['time'] > 0);
        $this->assertTrue($gotSummary['memory'] > 0);
        $this->assertTrue($gotSummary['to_fastest'] > 0);
        $this->assertTrue($gotSummary['to_least_memory'] > 0);
        $this->assertTrue($gotSummary['per_sec'] > 0);
    }
}

class TestBench
{
    /**
     * Iterations.
     *
     * @var int
     */
    private $iterations = 0;

    public function bench1($iterations)
    {
        $this->iterations = $iterations;
    }

    /**
     * @return int
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * Reset call counter.
     */
    public function resetIterations()
    {
        $this->iterations = 0;
    }
}
