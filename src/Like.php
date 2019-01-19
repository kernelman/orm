<?php
/**
 * Class Like
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:22 AM
 */

namespace Orm;


class Like extends Modifier
{

    /**
     * Get condition.
     *
     * @return string
     */
    public function call() {
        return " LIKE '" . Structures::addSlashes($this->structure) . "'";
    }
}
