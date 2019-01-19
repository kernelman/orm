<?php
/**
 * Class RegExp
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:52 AM
 */

namespace Orm;


class RegExp extends Modifier
{

    /**
     * @return string
     */
    public function call() {
        return " REGEXP '" . Structures::addSlashes($this->structure) . "'";
    }
}
