<?php
/**
 * Class OrmAbs
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    1:04 AM
 */

namespace Orm;

abstract class OrmAbs
{

    public $connect;
    public $options;

    /**
     * OrmAbs constructor.
     */
    public function __construct() {}

    /**
     * Character escapes
     *
     * @param $string
     * @return mixed
     */
    public function escape($string) {
        if(isset($this->connect)) {
            return $this->connect->escape($string);
        }

        return $string;
    }
}
