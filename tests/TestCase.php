<?php
/**
 * Class TestCase
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/16/19
 * Time:    9:40 PM
 */

namespace Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{

    public function __construct($name = null, array $data = [], string $dataName = '') {
        include_once dirname(__DIR__) . '/index.php';
        parent::__construct($name, $data, $dataName);
    }
}