<?php
/**
 * Class ORM
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    7:29 AM
 */

namespace Orm;

use Exceptions\NotFoundException;

class CoRoutineOrm extends OrmAbs
{
    public $pool;

    /**
     * CoRoutineOrm constructor.
     * @throws NotFoundException
     */
    public function __construct() {
        // Check swoole extension
        if (!extension_loaded('swoole')) {
            throw new NotFoundException('The swoole extension can not loaded.');
        }
    }

    /**
     * Connect to server
     *
     * @param $config
     * @return $this
     * @throws \Exceptions\UnconnectedException
     */
    public function connect($config) {
        $this->options  = $config;
        $this->pool     = CoRoutinePool::getInstance($config);
        $this->connect  = $this->pool->get();
        return $this;
    }

    /**
     *  Close connect.
     */
    public function close() {
        $this->connect->close();
    }

    public function recycle() {
        $this->pool->recycle($this->connect);
    }

    /**
     * @param $name
     * @return CoRoutineAction
     */
    public function __get($name) {
        return new CoRoutineAction($this, $name);
    }

    /**
     * Get table object
     *
     * @param $name
     * @return CoRoutineAction
     */
    public function table($name) {
        return new CoRoutineAction($this, $name);
    }

    /**
     * Run Query and get result
     *
     * @param $sql
     * @return Result
     */
    public function query($sql){
        $result = $this->connect->query($sql);
        if($result === true){
            $result = $this->connect->affected_rows;
        }

        return new Result($this, $result);
    }

    /**
     * Begin a transaction
     *
     */
    public function begin(){
        $this->connect->begin();
    }

    /**
     * Commit the transaction
     *
     */
    public function commit(){
        $this->connect->commit();
    }

    /**
     * Rollback the transaction
     *
     */
    public function rollback(){
        $this->connect->rollback();
    }
}
