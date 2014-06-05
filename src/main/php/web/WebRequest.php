<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\web;
use stubbles\input\Request;
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
    public function protocolVersion();

    /**
     * returns HTTP protocol version of request
     *
     * In case the version is not HTTP/1.0 or HTTP/1.1 return value is <null>.
     *
     * @return  string
     * @since   2.0.2
     * @deprecated  since 3.0.0, use protocolVersion() instead, will be removed with 4.0.0
     */
    public function getProtocolVersion();

    /**
     * returns the uri of the request
     *
     * @return  stubbles\peer\http\HttpUri
     */
    public function uri();

    /**
     * returns the uri of the request
     *
     * @return  stubbles\peer\http\HttpUri
     * @deprecated  since 3.0.0, use uri() instead, will be removed with 4.0.0
     */
    public function getUri();

    /**
     * return a list of all header names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function headerNames();

    /**
     * return an array of all header names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     * @deprecated  since 3.0.0, use headerNames() instead, will be removed with 4.0.0
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
     * @return  stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function headerErrors();

    /**
     * checks whether a request value from headers is valid or not
     *
     * @param   string  $headerName  name of header
     * @return  stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateHeader($headerName);

    /**
     * returns request value from headers for filtering or validation
     *
     * @param   string  $headerName  name of header
     * @return  stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readHeader($headerName);

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function cookieNames();

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     * @deprecated  since 3.0.0, use cookieNames() instead, will be removed with 4.0.0
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
     * @return  stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function cookieErrors();

    /**
     * checks whether a request value from cookie is valid or not
     *
     * @param   string  $cookieName  name of cookie
     * @return  stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateCookie($cookieName);

    /**
     * returns request value from cookies for filtering or validation
     *
     * @param   string  $cookieName  name of cookie
     * @return  stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readCookie($cookieName);

    /**
     * checks whether a request body is valid or not
     *
     * @return  stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateBody();

    /**
     * returns error collection for request body
     *
     * @return  stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function bodyErrors();

    /**
     * returns request body for filtering or validation
     *
     * @return  stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readBody();
}
