<?php
/**
 * Mysql config
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/17/19
 * Time:    3:43 PM
 */

return (object)[

    'host'      => '127.0.0.1',
    'port'      => 3306,
    'user'      => 'user',
    'password'  => 'user',
    'database'  => 'orm',
    'charset'   => 'utf8',
    'timeout'   => 5,           // Connection timeout, default set SW_MYSQL_CONNECT_TIMEOUT(1.0).
    'debug'     => true,
    'prefix'    => ''
];