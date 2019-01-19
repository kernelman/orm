<?php
/**
 * Class In
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    1:44 AM
 */

namespace Orm;

class In extends Modifier {

    /**
     * Get in structures.
     *
     * @return string
     */
    public function call() {
        return " IN (" . Structures::make($this->structure) . ")";
    }
}
