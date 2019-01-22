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

class AsyncOrm extends OrmAbs
{
    public function __construct() {
        // Check swoole extension
        if (!extension_loaded('swoole')) {
            throw new NotFoundException('The swoole extension can not loaded.');
        }

        $this->connect = new \swoole_mysql();
    }

    /**
     * Connect to server
     *
     * @param $config
     * @param callable $callback function
     */
    public function connect($config, $callback) {
        $this->options = $config;
        $this->connect->connect($config, function(\swoole_mysql $db, $result) use ($callback) {
            $callback(new Result($this, $result, $db, true));
        });
    }

    /**
     *  Close connect.
     */
    public function close() {
        $this->connect->close();
    }

    /**
     * @param $name
     * @return AsyncAction
     */
    public function __get($name) {
        return new AsyncAction($this, $name);
    }

    /**
     * Get table object
     *
     * @param $name
     * @return AsyncAction
     */
    public function table($name) {
        return new AsyncAction($this, $name);
    }

    /**
     * Run Query and get result
     *
     * @param string $sql sql sentence
     * @param callable $callback callback function
     */
    public function query($sql, $callback){
        $this->connect->query($sql, function(\swoole_mysql $link, $result) use ($callback){
            if($result === true){
                $result = $link->affected_rows;
            }

            $callback(new Result($this, $result, $link));
        });
    }

    /**
     * Begin a transaction
     *
     * @param callable $callback callback function
     */
    public function begin($callback){
        $this->connect->begin(function(\swoole_mysql $link, $result) use ($callback){
            $callback(new Result($this, $result, $link));
        });
    }

    /**
     * Commit the transaction
     *
     * @param callable $callback callback function
     */
    public function commit($callback){
        $this->connect->commit(function(\swoole_mysql $link, $result) use ($callback){
            $callback(new Result($this, $result, $link));
        });
    }

    /**
     * Rollback the transaction
     *
     * @param callable $callback callback function
     */
    public function rollback($callback){
        $this->connect->rollback(function(\swoole_mysql $link, $result) use ($callback){
            $callback(new Result($this, $result, $link));
        });
    }
}
