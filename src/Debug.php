<?php
/**
 * Class Debug
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/27/19
 * Time:    6:40 PM
 */

namespace Orm;


use Common\Property;

class Debug
{

    protected const DEBUG = 'debug';

    public static function show($query, $option) {
        if (Property::reality($option[self::DEBUG])) {
            echo $query . PHP_EOL;
        }
    }
}