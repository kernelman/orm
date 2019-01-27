<?php
/**
 * Class AsyncAction
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:58 AM
 */

namespace Orm;


use Common\Property;

class AsyncAction extends OrmAbs
{
    use Combined;

    protected $orm;
    protected $table;
    protected $select;
    protected $where;
    protected $order;
    protected $limit;
    protected $group;

    protected const BY      = 'by';
    protected const OR      = 'OR';
    protected const AND     = 'AND';
    protected const NUM     = 'num';
    protected const TYPE    = 'type';
    protected const SIDE    = 'side';
    protected const CLAUSE  = 'clause';
    protected const OFFSET  = 'offset';
    protected const HAVING  = 'having';
    protected const SELECT  = 'SELECT * ';

    /**
     * Action constructor.
     *
     * @param $orm
     * @param $table
     */
    public function __construct($orm, $table) {
        OrmAbs::__construct();

        $this->orm      = $orm;
        $this->connect  = $orm->connect;
        $this->options  = $orm->options;
        $this->table    = $table;
        $this->select   = [];
        $this->where    = [];
        $this->order    = [];
        $this->limit    = [];
        $this->group    = [];
    }

    /**
     * Structures Where
     *
     * @param array|string $clause where clause
     * @param array ...$side
     * @return AsyncAction
     */
    public function where($clause, ...$side) {
        $wheres = clone $this;

        if(is_array($clause)) {

            $keys = array_keys($clause);

            foreach ($keys as $key){
                $wheres->where = $this->wherePush($wheres->where, $key, $clause[$key], self::AND);
            }
        }

        if(is_string($clause) && is_array($side)) {

            if (count($side) === 1) {
                $makeSide       = $side[0];
                $wheres->where  = $this->wherePush($wheres->where, $clause, $makeSide, self::AND);
            }

            if (count($side) === 2) {
                $symbol         = $side[0];
                $makeSide       = $side[1];
                $wheres->where  = $this->wherePush($wheres->where, $clause, $makeSide, self::AND, $symbol);
            }
        }

        return $wheres;
    }

    /**
     * Structures where or
     *
     * @param array|string $clause or clause
     * @param array ...$side
     * @return AsyncAction
     */
    public function whereOr($clause, ...$side) {
        $wheres = clone $this;
        if(is_array($clause)){
            $keys = array_keys($clause);

            foreach ($keys as $key) {
                $wheres->where = $this->wherePush($wheres->where, $key, $clause[$key], self::OR);
            }
        }

        if(is_string($clause) && is_array($side)) {

            if (count($side) === 1) {
                $makeSide       = $side[0];
                $wheres->where  = $this->wherePush($wheres->where, $clause, $makeSide, self::OR);
            }

            if (count($side) === 2) {
                $symbol         = $side[0];
                $makeSide       = $side[1];
                $wheres->where  = $this->wherePush($wheres->where, $clause, $makeSide, self::OR, $symbol);
            }
        }

        return $wheres;
    }

    /**
     * Structures select
     *
     * @param array|string $fields fields to select
     * @return AsyncAction
     */
    public function select($fields) {
        $selects = clone $this;
        if(!is_array($fields)) {
            $fields = explode(",", $fields);
        }

        $selects->select = array_merge($selects->select, $fields);
        return $selects;
    }

    /**
     * Structures order
     *
     * @param string|array $fields rules for sorting
     * @return AsyncAction
     */
    public function order($fields) {
        $orders = clone $this;
        if(!is_array($fields)) {
            $fields = explode(',', $fields);
        }

        $orders->order = array_merge($orders->order, $fields);
        return $orders;
    }

    /**
     * Structures limit
     *
     * @param int $num number
     * @param int $offset offset
     * @return AsyncAction
     */
    public function limit($num, $offset = 0) {
        $limits = clone $this;
        $limits->limit = [
            self::OFFSET    => $offset,
            self::NUM       => $num
        ];

        return $limits;
    }

    /**
     * Structures group by
     *
     * @param string $by by field
     * @param null $having having clause
     * @return AsyncAction
     */
    public function group($by, $having = null) {
        $groups = clone $this;
        $groups->group = [
            self::BY        => $by,
            self::HAVING    => $having
        ];
        return $groups;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTable() {
        $prefix = '';
        if(isset($this->options['prefix'])){
            $prefix = $this->options['prefix'];
        }
        return $prefix . $this->table;
    }

    /**
     * Find all select Results
     *
     * @param callable $callback callback function
     */
    public function find($callback){
        $query = $this->setSelect().$this->setTable().$this->setWhere().$this->setGroup().$this->setOrder().$this->setLimit();
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback){
            $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param string $columnName column to count
     * @param string $callback callback function
     */
    public function count($columnName, $callback = "") {
        if($callback == "") {
            $callback   = $columnName;
            $column     = "*";

        } else {
            $column = $columnName;
        }

        $query = "SELECT COUNT($column) ".$this->setTable().$this->setWhere().$this->setGroup().$this->setOrder().$this->setLimit();
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback) {
            $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param string $columnName column to sum
     * @param callable $callback callback function
     */
    public function sum($columnName, $callback){
        $query = "SELECT SUM($columnName) ".$this->setTable().$this->setWhere().$this->setGroup().$this->setOrder().$this->setLimit();
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback) {

            if(!$result) {
                return $callback(new Result($this->orm, $result, $db));
            }

            $result = Structures::makeResult($result[0], true);
            return $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param string $columnName column to get max in
     * @param callable $callback callback function
     */
    public function max($columnName, $callback){
        $query = "SELECT MAX($columnName) ".$this->setTable().$this->setWhere().$this->setGroup().$this->setOrder().$this->setLimit();
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback){
            if(!$result) {
                return $callback(new Result($this->orm, $result, $db));
            }

            $result = Structures::makeResult($result[0]);
            return $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param string $columnName column to get min in
     * @param callable $callback callback function
     */
    public function min($columnName, $callback){
        $query = "SELECT MIN($columnName) ".$this->setTable().$this->setWhere().$this->setGroup().$this->setOrder().$this->setLimit();
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback){
            if(!$result) {
                return $callback(new Result($this->orm, $result, $db));
            }

            $result = Structures::makeResult($result[0]);
            return $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param string $columnName by column
     * @param mixed $value value to search for
     * @param callable $callback callback function
     */
    public function getBy($columnName, $value, $callback){
        $query = $this->setSelect() . $this->setTable()."WHERE $columnName = '".addslashes($value)."'";
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback) {
            $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param int $id Value of Primary Key
     * @param callable $callback callback function
     */
    public function get($id, $callback) {
        if(is_array($id)) {
            $gets = clone $this;
            $gets->where($id)->find($callback);
        }

        if (is_string($id) || is_int($id)) {
            $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->options['database']."' AND TABLE_NAME = '".$this->getTable()."' AND COLUMN_KEY = 'PRI'";
            Debug::show($query, $this->options);

            $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback, $id){
                $result = new Result($this->orm, $result, $db);
                if($result->status && isset($result->results[0]['COLUMN_NAME'])){
                    $key    = $result->results[0]['COLUMN_NAME'];
                    $query  = $this->setSelect() . $this->setTable() . "WHERE $key = '$id'";
                    Debug::show($query, $this->options);

                    $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback){
                        $callback(new Result($this->orm, $result, $db));
                    });

                } else{
                    $callback($result);
                }
            });
        }
    }

    /**
     * @param array $data data for update
     * @param callable $callback callback function
     */
    public function update($data, $callback){
        $sets = "";
        $keys = array_keys($data);

        foreach ($keys as $key){
            if(!is_object($data[$key])) {
                $sets.= $key." = '".addslashes($data[$key])."', ";

            }else{
                $sets.= $key.($data[$key]->call()).", ";
            }
        }

        if($sets != "") {
            $sets = substr($sets, 0, -2);
        }
        $query = "UPDATE ".$this->getTable()." SET ".$sets." ".$this->setWhere().$this->setGroup().$this->setOrder().$this->setLimit();
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback){
            if(!$result){
                $result = $db->affected_rows;
            }
            $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param array $data data to insert
     * @param callable $callback callback function
     */
    public function insert($data, $callback){
        $fields = ""; $values = "";
        $keys = array_keys($data);
        foreach ($keys as $key){
            $fields.= $key.", ";
            $values.= "'".addslashes($data[$key])."', ";
        }

        if($fields != "") {
            $fields = substr($fields, 0, -2);
        }

        if($values != "") {
            $values = substr($values, 0, -2);
        }

        $query = "INSERT INTO ".$this->getTable()." ($fields) VALUES ($values)";
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback){
            if(!$result){
                $result = $db->affected_rows;
            }
            $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param callable $callback function
     */
    public function delete($callback){
        $query = "DELETE ".$this->setTable().$this->setWhere().$this->setGroup().$this->setOrder().$this->setLimit();
        Debug::show($query, $this->options);

        $this->connect->query($query, function(\swoole_mysql $db, $result) use ($callback){
            if(!$result){
                $result = $db->affected_rows;
            }
            $callback(new Result($this->orm, $result, $db));
        });
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {
        if(strpos($name, "getBy") == 0 && count($arguments) == 2) {
            $columns    = substr($name, 5);
            $columns[0] = strtolower($columns[0]);

            for($i = 0; $i<strlen($columns); ++$i) {
                $column = strtolower($columns[$i]);

                if($column !=$columns[$i]) {
                    $columns[$i] = $column;
                    $columns = substr_replace($columns,"_", $i,0);
                    $i++;
                }
            }

            $this->getBy($columns, $arguments[0], $arguments[1]);
        }
    }
}
