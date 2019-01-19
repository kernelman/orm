<?php
/**
 * Class Literal
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:27 AM
 */

namespace Orm;


class Literal extends Modifier
{
    /**
     * Get condition.
     *
     * @return string
     */
    public function call() {
        return " = " . $this->structure;
    }
}
