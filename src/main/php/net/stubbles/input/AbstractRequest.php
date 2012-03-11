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
use net\stubbles\lang\BaseObject;
/**
 * Abstract base class for requests.
 */
abstract class AbstractRequest extends BaseObject implements Request
{
    /**
     * list of params
     *
     * @type  Params
     */
    private $params;
    /**
     * switch whether request has been cancelled or not
     *
     * @type  bool
     */
    private $isCancelled   = false;

    /**
     * constructor
     *
     * @param  Params  $params
     */
    public function __construct(Params $params)
    {
        $this->params = $params;
    }

    /**
     * cancels the request, e.g. if it was detected that it is invalid
     *
     * @return  Request
     */
    public function cancel()
    {
        $this->isCancelled = true;
        return $this;
    }

    /**
     * checks whether the request has been cancelled or not
     *
     * @return  bool
     */
    public function isCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * return an array of all param names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function getParamNames()
    {
        return $this->params->getNames();
    }

    /**
     * returns list of errors for request parameters
     *
     * @return  ParamErrors
     * @since   1.3.0
     */
    public function paramErrors()
    {
        return $this->params->errors();
    }

    /**
     * checks whether a request param is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasParam($paramName)
    {
        return $this->params->has($paramName);
    }

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @param   string  $paramName  name of parameter
     * @return  ValueValidator
     * @since   1.3.0
     */
    public function validateParam($paramName)
    {
        return new ValueValidator($this->params->get($paramName));
    }

    /**
     * returns request value from params for filtering or validation
     *
     * @param   string  $paramName  name of parameter
     * @return  ValueFilter
     * @since   1.3.0
     */
    public function readParam($paramName)
    {
        return new ValueFilter($this->params->errors(),
                               $this->params->get($paramName)
        );
    }
}
?>