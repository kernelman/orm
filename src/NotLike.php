<?php
/**
 * Class NotLike
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:45 AM
 */

namespace Orm;


class NotLike extends Modifier
{

    /**
     * Call not like structures
     *
     * @return string
     */
    public function call() {
        return " NOT LIKE '" . Structures::make($this->structure) . "'";
    }
}
