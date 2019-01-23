<?php
/**
 * Class Result
 *
 * Author:  Kernel Huang
 * Mail:    kernelman79@gmail.com
 * Date:    1/19/19
 * Time:    1:13 AM
 */

namespace Orm;


use Common\Property;

class Result extends OrmAbs
{
    public $orm;
    public $links;
    public $connect;
    public $results;

    /**
     * Result constructor.
     *
     * @param $orm object
     * @param $results
     * @param $link AsyncOrm required assign value, CoRoutineOrm no need to assign value.
     * @param bool $connect
     */
    public function __construct($orm, $results, $link = null, $connect = false) {
        $this->orm      = $orm;
        $this->links    = $link;
        $this->results  = $results;
        $this->connect  = $connect;
    }

    /**
     * @return bool|mixed|object
     */
    public function toObject() {
        if (Property::reality($this->results)) {

            if (count($this->results) === 1) {
                return (object)$this->results;
            }
            return json_decode(json_encode($this->results));
        }

        return false;
    }

    /**
     * @return bool|array
     */
    public function toArray() {
        if (Property::reality($this->results)) {

            if (count($this->results) === 1) {
                return $this->results[0];
            }
            return $this->results;
        }

        return false;
    }

    /**
     * Get status for ResultSet
     *
     * @return bool
     */
    public function getStatus() {
        return Property::realityReturnTrue($this->results);
    }

    /**
     * Get Result for ResultSet
     *
     * @return mixed
     */
    public function getResult() {
        return Property::reality($this->results);
    }

    /**
     * Get error code for ResultSet
     *
     * @return int
     */
    public function getErrorCode() {
        if($this->results === false) {
            return $this->checkConnectAndError($this->links->connect_errno, $this->links->errno);
        }

        return 0;
    }

    /**
     * Get error message for ResultSet
     *
     * @return int|null
     */
    public function getErrorMsg() {
        if($this->results === false) {
            return $this->checkConnectAndError($this->links->connect_errno, $this->links->error);
        }

        return 0;
    }

    /**
     * Get method, magic methods.
     *
     * @param $method
     * @return bool|int|mixed|null
     */
    public function __get($method) {

        switch ($method) {
            case "status":
                $result = $this->getStatus();
                break;

            case "result":
                $result = $this->getResult();
                break;

            case "errorCode":
                $result = $this->getErrorCode();
                break;

            case "errorMsg":
                $result = $this->getErrorMsg();
                break;

            case "orm":
                $result = $this->orm;
                break;

            default:
                $result = false;
                break;
        }

        return $result;
    }

    /**
     * Check connect state.
     *
     * @param $error
     * @param $state
     * @return null
     */
    private function checkConnectAndError($error, $state) {
        return $this->connect ? Property::notExistsReturnZero($error) : Property::notExistsReturnNull($state);
    }
}
