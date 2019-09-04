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


use Common\Property;
use Exceptions\NotFoundException;

class SyncOrm extends OrmAbs
{
    public function __construct() {
        // Check PDO extension
        if (!extension_loaded('PDO')) {
            throw new NotFoundException('The PDO extension can not loaded.');
        }
    }

    /**
     * Connect from PDO to server
     *
	 * @param $config
	 * @return $this
	 */
    public function connect($config) {
    	$this->options = $config;
    	$host       = $this->options['host'] ?? '';
	    $port       = $this->options['port'] ?? 3306;
	    $user       = $this->options['user'] ?? '';
	    $password   = $this->options['password'] ?? '';
	    $database   = $this->options['database'] ?? '';
	    $charset    = $this->options['charset'] ?? 'utf8';
	    $option     = [ \PDO::ATTR_PERSISTENT => true, \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];

	    $dsn = 'mysql:host=' . $host .';port=' .$port .';dbname=' .$database .';charset=' . $charset;
	    try {
		    $this->connect = new \PDO($dsn, $user, $password, $option);
		    $this->connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
	    }
	    catch (\PDOException $e) {
	    	echo 'PDO Error: ' . $e . PHP_EOL;
	    }

	    return $this;
    }

    /**
     *  Close connect.
     */
    public function close() {
        $this->connect->close();
    }

    /**
	 * @param $name
	 * @return SyncAction
	 */
    public function __get($name) {
        return new SyncAction($this, $name);
    }

    /**
     * Get table object
     *
     * @param $name
     * @return SyncAction
     */
    public function table($name) {
        return new SyncAction($this, $name);
    }

    /**
     * Run Query and get result
     *
	 * @param $sql
	 * @return Result
	 */
    public function query($sql){
	    $result = $this->connect->prepare($sql);
	    $result->execute();
	    if($result === true){
		    $result = $result->fetchAll(\PDO::FETCH_ASSOC);
	    }

	    return new Result($this, $result);
    }

	/**
	 * Begin a transaction
	 *
	 */
	public function begin(){
		$this->connect->beginTransaction();
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
