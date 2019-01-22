<?php
/**
 * Class UserModelTest
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    11:41 AM
 */

namespace Tests;


use Orm\CoRoutineOrm;
use Services\Config;

class CoRoutineOrmTest extends TestCase {

    public function testUser() {
        $conf = (array)Config::mysqlpool()::got();

        go (function () use ($conf) {
            $id     = 30;
            $orm    = new CoRoutineOrm();

            $result = $orm->connect($conf)->table('users')->select('id, username')->where(['id' => $id])->find();
            if ($result->status) {
                $this->assertEquals(30, (int)$result->toObject()->id);
            }

            $result = $orm->connect($conf)->query('SELECT id,name FROM users WHERE id = ' . $id);
            if ($result->status) {
                $this->assertEquals(30, (int)$result->toObject()->id);
            }

            $result = $orm->connect($conf)->table('users')->select('id, username')->get($id);
            if ($result->status) {
                $this->assertEquals(30, (int)$result->toObject()->id);
            }

            $result = $orm->connect($conf)->table('users')->select('id')->get(1);
            $this->assertFalse($result->status);

            $orm->recycle($orm); // Recycle connect
        });
    }
}
