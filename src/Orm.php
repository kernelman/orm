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


class Orm extends OrmAbs
{
    public function __construct() {
        $this->connect = new \swoole_mysql();
    }

    /**
     * Connect to server
     *
     * @param array $server
     * @param callable $callback callback function
     */
    public function connect($server, $callback) {
        $this->options = $server;
        $this->connect->connect($server, function(\swoole_mysql $db, $result) use ($callback) {
            $callback(new Result($this, $db, $result, true));
        });
    }

    /**
     *  Disconnect
     */
    public function disconnect() {
        $this->connect->close();
    }

    /**
     * @param $name
     * @return Action
     */
    public function __get($name) {
        return new Action($this, $name);
    }

    /**
     * Get table object
     *
     * @param $name
     * @return Action
     */
    public function table($name) {
        return new Action($this, $name);
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

            $callback(new Result($this, $link, $result));
        });
    }

    /**
     * Begin a transaction
     *
     * @param callable $callback callback function
     */
    public function begin($callback){
        $this->connect->begin(function(\swoole_mysql $link, $result) use ($callback){
            $callback(new Result($this, $link, $result));
        });
    }

    /**
     * Commit the transaction
     *
     * @param callable $callback callback function
     */
    public function commit($callback){
        $this->connect->commit(function(\swoole_mysql $link, $result) use ($callback){
            $callback(new Result($this, $link, $result));
        });
    }

    /**
     * Rollback the transaction
     *
     * @param callable $callback callback function
     */
    public function rollback($callback){
        $this->connect->rollback(function(\swoole_mysql $link, $result) use ($callback){
            $callback(new Result($this, $link, $result));
        });
    }
}
