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

use Kicaj\Bench\Timer;

/**
 * TimerTest.
 *
 * @coversDefaultClass \Kicaj\Bench\Timer
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class TimerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Timer
     */
    protected $timer;

    protected function setUp()
    {
        parent::setUp();

        $this->timer = new Timer;
    }

    /**
     * @covers ::make
     * @covers ::__construct
     */
    public function test_make()
    {
        $timer = Timer::make();
        $this->assertInstanceOf('\Kicaj\Bench\Timer', $timer);
    }

    /**
     * @covers ::start
     * @covers ::stop
     * @covers ::memory_usage
     */
    public function test_start()
    {
        $this->timer->start('bench1');
        $this->timer->stop('bench1');

        $got = $this->timer->get('bench1');

        $this->assertTrue($got['start'] > 0);
        $this->assertTrue($got['stop'] > 0);
        $this->assertTrue($got['time'] > 0);
        $this->assertTrue($got['memory'] > 0);
        $this->assertSame(1, $got['executions']);
    }

    /**
     * @covers ::start
     * @covers ::stop
     */
    public function test_start_data()
    {
        $this->timer->start('bench1', ['key' => 'value']);
        $this->timer->stop('bench1');

        $ret = $this->timer->get('bench1');
        $this->assertSame([['key' => 'value']], $ret['data']);
    }

    /**
     * @covers ::start
     * @covers ::stop
     * @covers ::get
     */
    public function test_start_twoSameTimers()
    {
        // First
        $this->timer->start('bench1');
        $this->timer->stop('bench1');

        // Second
        $this->timer->start('bench1');
        $this->timer->stop('bench1');

        $got = $this->timer->getAll();

        $this->assertSame(1, count($got));
        $this->assertArrayHasKey('bench1', $got);

        $got = $got['bench1'];
        $this->assertTrue($got['start'] > 0);
        $this->assertTrue($got['stop'] > 0);
        $this->assertTrue($got['time'] > 0);
        $this->assertTrue($got['memory'] > 0);
        $this->assertSame(2, $got['executions']);
    }

    /**
     * @expectedException \Kicaj\Bench\BenchEx
     * @expectedExceptionMessage timer "bench1" already started
     *
     * @covers ::start
     */
    public function test_start_startTwiceWithoutStopping()
    {
        $this->timer->start('bench1');
        $this->timer->start('bench1');
    }

    /**
     * @expectedException \Kicaj\Bench\BenchEx
     * @expectedExceptionMessage timer "notExisting" does not exist (stop)
     *
     * @covers ::stop
     */
    public function test_stop_notExisting()
    {
        $this->timer->stop('notExisting');
    }

    /**
     * @covers ::stop
     * @covers ::get
     */
    public function test_stop_withData()
    {
        $this->timer->start('bench1');
        $this->timer->stop('bench1', ['key' => 'value']);

        $got = $this->timer->get('bench1');

        $this->assertArrayHasKey('data', $got);

        $data = $got['data'];

        $this->assertSame(1, count($data));
        $this->assertArrayHasKey('key', $data[0]);
        $this->assertSame('value', $data[0]['key']);
        $this->assertTrue($data[0]['time'] > 0);
    }

    /**
     * @expectedException \Kicaj\Bench\BenchEx
     * @expectedExceptionMessage timer "notExisting" does not exist (get)
     *
     * @covers ::get
     */
    public function test_get_notExisting()
    {
        $this->timer->get('notExisting');
    }

    /**
     * @covers ::clear
     * @covers ::getAll
     */
    public function test_clear()
    {
        $this->timer->start('bench1');
        $this->timer->stop('bench1');
        $this->timer->start('bench2');
        $this->timer->stop('bench2');

        $this->assertSame(2, count($this->timer->getAll()));

        $timer = $this->timer->clear();

        $this->assertSame($this->timer, $timer);
        $this->assertSame([], $this->timer->getAll());
    }

    /**
     * @covers ::delete
     */
    public function test_delete()
    {
        $this->timer->start('bench1');
        $this->timer->stop('bench1');
        $this->timer->start('bench2');
        $this->timer->stop('bench2');

        $this->assertSame(['bench1', 'bench2'], array_keys($this->timer->getAll()));

        $success = $this->timer->delete('bench1');

        $this->assertTrue($success);
        $this->assertSame(['bench2'], array_keys($this->timer->getAll()));
    }

    /**
     * @covers ::delete
     */
    public function test_delete_notExisting()
    {
        $success = $this->timer->delete('notExisting');

        $this->assertFalse($success);
    }
}
