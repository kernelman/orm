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


use Orm\AsyncOrm;
use Services\Config;

class AsyncOrmTest extends TestCase {

    /**
     * @throws \Exceptions\NotFoundException
     */
    public function testGetUser() {
        $config = (array)Config::mysql()::got();
        $id     = 30;
        $orm    = new AsyncOrm();
        $orm->connect($config, function ($result) use($orm, $id) {

            if($result->status) {
                $orm->table('users')->select('id, username')->get($id, function ($result) {
                    $this->assertEquals('30', (int)$result->results[0]['id']);
                    exit($result->errorCode);
                });
            }

            if ($result->errorCode == 110) {
                $this->assertEquals(110, $result->errorCode);
                echo 'Connection timed: Please check host setting';
                exit($result->errorCode);
            }

            if ($result->errorCode == 111) {
                $this->assertEquals(111, $result->errorCode);
                echo 'Connection timed: Please check port setting';
                exit($result->errorCode);
            }
        });
        $orm->close();

        $id = 1;
        $orm->connect($config, function ($result) use($orm, $id) {

            if ($result->status) {
                // Use string to where test
                $orm->table('admins')->select('id, username')->where("id = $id")->find(function ($result) {
                    $this->assertEquals('1', (int)$result->results[0]['id']);
                    exit($result->errorCode);
                });
            }
        });
        $orm->close();

        $id = 1;
        $orm->connect($config, function ($result) use($orm, $id) {

            if ($result->status) {
                // Use array to where
                $orm->table('admins')->select('id, username')->where([ 'id' => $id ])->find(function ($result) {
                    $this->assertEquals('1', (int)$result->results[0]['id']);
                    exit($result->errorCode);
                });
            }
        });
        $orm->close();
    }
}
