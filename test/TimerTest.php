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
     * @covers ::start
     */
    public function test_start()
    {
        $this->timer->start('bench1');
        $this->timer->stop('bench1');

        $ret = $this->timer->get('bench1');
        $this->assertTrue($ret['time'] > 0);
    }

    /**
     * @covers ::start
     */
    public function test_start_data()
    {
        $this->timer->start('bench1', ['key' => 'value']);
        $this->timer->stop('bench1');

        $ret = $this->timer->get('bench1');
        $this->assertSame([['key' => 'value']], $ret['data']);
    }
}
