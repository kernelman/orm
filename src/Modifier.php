<?php
/**
 * Class Modifier
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    7:09 AM
 */

namespace Orm;


abstract class Modifier
{

    protected $structure;

    /**
     * In constructor.
     *
     * @param $struct
     */
    public function __construct($struct) {
        $this->structure = $struct;
    }
}
