<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input;
use stubbles\input\errors\ParamErrors;
/**
 * Interface for handling input data.
 *
 * @api
 */
interface Request
{
    /**
     * cancels the request, e.g. if it was detected that it is invalid
     *
     * @return  Request
     * @deprecated  since 3.0.0, will be removed with 4.0.0
     */
    public function cancel();

    /**
     * checks whether the request has been cancelled or not
     *
     * @return  bool
     * @deprecated  since 3.0.0, will be removed with 4.0.0
     */
    public function isCancelled();

    /**
     * returns the request method
     *
     * @return  string
     */
    public function method();

    /**
     * returns the request method
     *
     * @return  string
     * @deprecated  since 3.0.0, use method() instead, will be removed with 4.0.0
     */
    public function getMethod();

    /**
     * return a list of all param names registered in this request
     *
     * @return  string[]
     */
    public function paramNames();

    /**
     * return an array of all param names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     * @deprecated  since 3.0.0, use paramNames() instead, will be removed with 4.0.0
     */
    public function getParamNames();

    /**
     * returns list of errors for request parameters
     *
     * @return  ParamErrors
     * @since   1.3.0
     */
    public function paramErrors();

    /**
     * checks whether a request param is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasParam($paramName);

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @param   string  $paramName  name of request value
     * @return  stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateParam($paramName);

    /**
     * returns request value from params for validation
     *
     * @param   string  $paramName  name of request value
     * @return  stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readParam($paramName);
}
