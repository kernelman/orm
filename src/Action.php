<?php
/**
 * Class Action
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:58 AM
 */

namespace Orm;


use Common\Property;

class Action extends OrmAbs
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
    protected const DEBUG   = 'debug';
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

    private function wherePush($where, $clause, $side, $type) {
        return array_push($where, [
                self::TYPE      => $type,
                self::CLAUSE    => $clause,
                self::SIDE      => $side
            ]
        );
    }

    /**
     * Structures Where
     *
     * @param array|string $clause where clause
     * @param array ...$side
     * @return Action
     */
    public function where($clause, ...$side) {
        $wheres = clone $this;

        if(is_array($clause)) {

            $keys = array_keys($clause);

            foreach ($keys as $key){
                $wheres->where = $this->wherePush($wheres->where, $key, $clause[$key], self::AND);
            }
        }

        if(is_string($clause)) {
            $wheres->where = $this->wherePush($wheres->where, $clause, $side, self::AND);
        }

        return $wheres;
    }

    /**
     * Structures where or
     *
     * @param array|string $clause or clause
     * @param array ...$side
     * @return Action
     */
    public function whereOr($clause, ...$side) {
        $wheres = clone $this;
        if(is_array($clause)){
            $keys = array_keys($clause);

            foreach ($keys as $key){
                $wheres->where = $this->wherePush($wheres->where, $key, $clause[$key], self::OR);
            }
        }

        if(is_string($clause)) {
            $wheres->where = $this->wherePush($wheres->where, $clause, $side, self::OR);
        }

        return $wheres;
    }

    /**
     * Structures select
     *
     * @param array|string $fields fields to select
     * @return Action
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
     * @return Action
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
     * @return Action
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
     * @return Action
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
     * Fetch all select Results
     *
     * @param callable $callback callback function
     */
    public function fetch($callback){
        $query = $this->genSelect().$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback){
            $callback(new Result($this->orm, $link, $result));
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

        $query = "SELECT COUNT($column) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();

        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback) {
            $callback(new Result($this->orm, $link, $result));
        });
    }

    /**
     * @param string $columnName column to sum
     * @param callable $callback callback function
     */
    public function sum($columnName, $callback){
        $query = "SELECT SUM($columnName) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();

        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback) {

            if(!$result) {
                return $callback(new Result($this->orm, $link, $result));
            }

            $result = Structures::makeResult($result[0], true);
            return $callback(new Result($this->orm, $link, $result));
        });
    }

    /**
     * @param string $columnName column to get max in
     * @param callable $callback callback function
     */
    public function max($columnName, $callback){
        $query = "SELECT MAX($columnName) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback){
            if(!$result) {
                return $callback(new Result($this->orm, $link, $result));
            }

            $result = Structures::makeResult($result[0]);
            return $callback(new Result($this->orm, $link, $result));
        });
    }

    /**
     * @param string $columnName column to get min in
     * @param callable $callback callback function
     */
    public function min($columnName, $callback){
        $query = "SELECT MIN($columnName) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback){
            if(!$result) {
                return $callback(new Result($this->orm, $link, $result));
            }

            $result = Structures::makeResult($result[0]);
            return $callback(new Result($this->orm, $link, $result));
        });
    }

    /**
     * @param string $columnName by column
     * @param mixed $value value to search for
     * @param callable $callback callback function
     */
    public function getBy($columnName, $value, $callback){
        $query = self::SELECT .$this->genTable()."WHERE $columnName = '".addslashes($value)."'";
        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback) {
            $callback(new Result($this->orm, $link, $result));
        });
    }

    /**
     * @param int $id Value of Primary Key
     * @param callable $callback callback function
     */
    public function get($id, $callback){
        if(is_array($id)){
            $gets = clone $this;
            $gets->where($id)->fetch($callback);

        } else{
            $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->options['database']."' AND TABLE_NAME = '".$this->getTable()."' AND COLUMN_KEY = 'PRI'";
            if (Property::reality($this->options[self::DEBUG])) {
                echo $query . PHP_EOL;
            }

            $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback, $id){
                $result = new Result($this->orm, $link, $result);
                if($result->status && isset($result->results[0]['COLUMN_NAME'])){
                    $key    = $result->results[0]['COLUMN_NAME'];
                    $query  = self::SELECT . $this->genTable() . "WHERE $key = '$id'";

                    if (Property::reality($this->options[self::DEBUG])) {
                        echo $query . PHP_EOL;
                    }

                    $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback){
                        $callback(new Result($this->orm, $link, $result));
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
            if(gettype($data[$key]) != "object"){
                $sets.= $key." = '".addslashes($data[$key])."', ";
            }else{
                $sets.= $key.($data[$key]->call()).", ";
            }
        }

        if($sets != "") {
            $sets = substr($sets, 0, -2);
        }
        $query = "UPDATE ".$this->getTable()." SET ".$sets." ".$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();

        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback){
            if(!$result){
                $result = $link->affected_rows;
            }
            $callback(new Result($this->orm, $link, $result));
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
        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback){
            if(!$result){
                $result = $link->affected_rows;
            }
            $callback(new Result($this->orm, $link, $result));
        });
    }

    /**
     * @param callable $callback callback function
     */
    public function delete($callback){
        $query = "DELETE ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if (Property::reality($this->options[self::DEBUG])) {
            echo $query . PHP_EOL;
        }

        $this->connect->query($query, function(\swoole_mysql $link, $result) use ($callback){
            if(!$result){
                $result = $link->affected_rows;
            }
            $callback(new Result($this->orm, $link, $result));
        });
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {
        if(strpos($name, "getBy") == 0 && sizeof($arguments) == 2) {
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
