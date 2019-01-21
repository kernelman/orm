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


use Orm\Orm;
use Services\Config;

class UserTest extends TestCase {

    public function testGetUser() {
        $config = Config::mysql()::got();
        $id     = 30;
        $orm    = new Orm();
        $conf   = (array)$config;
        $orm->connect((array)$conf, function ($result) use($orm, $id) {

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
    }
}
