<?php
/**
 * Class Combined
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    7:20 AM
 */

namespace Orm;


/**
 * Trait Combined
 * Combined action
 *
 * @package Orm
 */
trait Combined
{

    /**
     * @return string
     */
    private function setSelect() {
        if(count($this->select) == 0){
            return self::SELECT;
        }

        $selects = "";
        foreach ($this->select as $item){
            $selects.=trim($item).", ";
        }

        $selects = substr($selects, 0, -2);
        return "SELECT " . $selects . " ";
    }

    /**
     * @return string
     */
    private function setTable() {
        return "FROM ". $this->getTable() . " ";
    }

    /**
     * @return string
     */
    private function setWhere() {
        if(count($this->where) == 0) {
            return "";
        }

        $wheres = "";
        foreach ($this->where as $item) {
            $addon = $item[self::TYPE] . " ";
            if($wheres == "") {
                $addon = "";
            }

            if(is_array($item[self::SIDE])) {
                $wheres .= $addon.$item[self::CLAUSE] . " ";

                foreach ($item[self::SIDE] as $sideItem) {
                    // $wheres = Structures::strReplace("?", "'" . addslashes($sideItem) . "'", $wheres);
                    $wheres = "'" . addslashes($sideItem) . "'" . $wheres;
                }

            } else{

                if(gettype($item[self::SIDE]) != "object") {
                    $wheres .= $addon.$item[self::CLAUSE] . " = '" . addslashes($item[self::SIDE]) . "' ";

                } else{
                    $wheres .= $addon . $item[self::CLAUSE] . ($item[self::SIDE]->call()) . " ";
                }
            }
        }

        return "WHERE " . $wheres;
    }

    /**
     * @return string
     */
    private function setGroup() {
        if(count($this->group) == 0) {
            return "";
        }

        $addon = "";
        if(!is_null($this->group[self::HAVING])) {
            $addon = " HAVING ".$this->group[self::HAVING];
        }

        return "GROUP BY " . $this->group[self::BY] . $addon . " ";
    }

    /**
     * @return string
     */
    private function setOrder() {
        if(count($this->order) == 0){
            return "";
        }

        $orders = "";
        foreach ($this->order as $item) {
            $orders .= trim($item) . ", ";
        }

        $orders = substr($orders, 0, -2);
        return "ORDER BY ". $orders . " ";
    }

    /**
     * @return string
     */
    private function setLimit() {
        if(count($this->limit) == 0){
            return '';
        }

        if($this->limit[self::OFFSET] == 0) {
            return "LIMIT " . $this->limit[self::NUM] . " ";
        }

        return "LIMIT " . $this->limit[self::OFFSET] . "," . $this->limit[self::NUM] . " ";
    }
}
