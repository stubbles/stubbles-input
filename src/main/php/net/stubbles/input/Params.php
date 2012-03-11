<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input;
use net\stubbles\input\error\ParamErrors;
use net\stubbles\lang\BaseObject;
/**
 * Interface for handling input data.
 */
class Params extends BaseObject
{
    /**
     * list of parameters
     *
     * @type  array
     */
    private $params;
    /**
     * list of errors for parameters
     *
     * @type  ParamErrors
     */
    private $errors;

    /**
     * constructor
     *
     * @param  array  $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * checks whether a request param is set
     *
     * @param   string  $paramName
     * @return  bool
     */
    public function has($paramName)
    {
        return isset($this->params[$paramName]);
    }

    /**
     * returns raw value of parameter or null if not set
     *
     * @param   string  $paramName
     * @return  Param
     */
    public function get($paramName)
    {
        if (!isset($this->params[$paramName])) {
            return new Param($paramName, null);
        }

        return new Param($paramName, $this->params[$paramName]);
    }

    /**
     * return an array of all param names registered in this request
     *
     * @return  string[]
     */
    public function getNames()
    {
        return array_keys($this->params);
    }

    /**
     * returns error collection for request parameters
     *
     * @return  ParamErrors
     */
    public function errors()
    {
        if (null === $this->errors) {
            $this->errors = new ParamErrors();
        }

        return $this->errors;
    }
}
?>