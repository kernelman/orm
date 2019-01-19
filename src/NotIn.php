<?php
/**
 * Class NotIn
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:31 AM
 */

namespace Orm;


class NotIn extends Modifier
{

    /**
     * Get not in structures.
     *
     * @return string
     */
    public function call() {
        return " NOT IN (" . Structures::make($this->structure) . ")";
    }
}
