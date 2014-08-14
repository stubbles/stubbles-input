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
/**
 * Interface for handling input data.
 *
 * @api
 */
interface Request
{
    /**
     * returns the request method
     *
     * @return  string
     */
    public function method();

    /**
     * return a list of all param names registered in this request
     *
     * @return  string[]
     */
    public function paramNames();

    /**
     * returns list of errors for request parameters
     *
     * @return  \stubbles\input\errors\ParamErrors
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
     * @return  \stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateParam($paramName);

    /**
     * returns request value from params for validation
     *
     * @param   string  $paramName  name of request value
     * @return  \stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readParam($paramName);
}
