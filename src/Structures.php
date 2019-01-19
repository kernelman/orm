<?php
/**
 * Class Structures
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    3:35 AM
 */

namespace Orm;


class Structures
{

    /**
     * Make condition
     *
     * @param $structure
     * @return bool|string
     */
    public static function make($structure) {

       $structures = '';

       if(is_array($structure)) {

           foreach ($structure as $item) {
               $structures .= "'$item', ";
           }

           if($structures != '' ) {
               $structures = substr($structures, 0, -2);
           }
       }

       if(is_string($structure)) {

           $structures = $structure;
       }

       return $structures;
    }

    /**
     * Call addslashes function.
     *
     * @param $structure
     * @return string
     */
    public static function addSlashes($structure) {
        return addslashes($structure);
    }

    /**
     * String replace
     *
     * @param $needle
     * @param $replace
     * @param $haystack
     * @return mixed
     */
    public static function strReplace($needle, $replace, $haystack) {
        $pos = strpos($haystack, $needle);

        if ($pos === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    /**
     * Make result.
     *
     * @param $results
     * @param bool $zero
     * @return bool|int|null
     */
    public static function makeResult($results, $zero = false) {

        $value = null;
        foreach ($results as $item) {
            $value = $item;
        }

        if(is_null($value)) {
            if ($zero) {
                $result = 0;

            } else {
                $result = false;
            }

        } else{
            $result = $value;
        }

        return $result;
    }
}
