<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\web;
use net\stubbles\input\Request;
/**
 * Interface for web applications requests.
 *
 * @api
 */
interface WebRequest extends Request
{
    /**
     * checks whether request was made using ssl
     *
     * @return  bool
     */
    public function isSsl();

    /**
     * returns HTTP protocol version of request
     *
     * In case the version is not HTTP/1.0 or HTTP/1.1 return value is <null>.
     *
     * @return  string
     * @since   2.0.2
     */
    public function getProtocolVersion();

    /**
     * returns the uri of the request
     *
     * @return  net\stubbles\peer\http\HttpUri
     */
    public function getUri();

    /**
     * return an array of all header names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function getHeaderNames();

    /**
     * checks whether a request header is set
     *
     * @param   string  $headerName
     * @return  bool
     * @since   1.3.0
     */
    public function hasHeader($headerName);

    /**
     * returns error collection for request headers
     *
     * @return  net\stubbles\input\ParamErrors
     * @since   1.3.0
     */
    public function headerErrors();

    /**
     * checks whether a request value from headers is valid or not
     *
     * @param   string  $headerName  name of header
     * @return  net\stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateHeader($headerName);

    /**
     * returns request value from headers for filtering or validation
     *
     * @param   string  $headerName  name of header
     * @return  net\stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readHeader($headerName);

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function getCookieNames();

    /**
     * checks whether a request cookie is set
     *
     * @param   string  $cookieName
     * @return  bool
     * @since   1.3.0
     */
    public function hasCookie($cookieName);

    /**
     * returns error collection for request cookies
     *
     * @return  net\stubbles\input\ParamErrors
     * @since   1.3.0
     */
    public function cookieErrors();

    /**
     * checks whether a request value from cookie is valid or not
     *
     * @param   string  $cookieName  name of cookie
     * @return  net\stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateCookie($cookieName);

    /**
     * returns request value from cookies for filtering or validation
     *
     * @param   string  $cookieName  name of cookie
     * @return  net\stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readCookie($cookieName);

    /**
     * checks whether a request body is valid or not
     *
     * @return  net\stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateBody();

    /**
     * returns error collection for request body
     *
     * @return  net\stubbles\input\ParamErrors
     * @since   1.3.0
     */
    public function bodyErrors();

    /**
     * returns request body for filtering or validation
     *
     * @return  net\stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readBody();
}
?>