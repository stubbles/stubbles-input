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
     * If no SERVER_PROTOCOL is present it is assumed that the protocol version
     * is HTTP/1.0. In case the SERVER_PROTOCOL does not denote a valid HTTP
     * version according to http://tools.ietf.org/html/rfc7230#section-2.6 the
     * return value will be null.
     *
     * @return  \stubbles\peer\http\HttpVersion
     * @since   2.0.2
     */
    public function protocolVersion();

    /**
     * returns HTTP protocol version of request
     *
     * In case the version is not HTTP/1.0 or HTTP/1.1 return value is <null>.
     *
     * @return  \stubbles\peer\http\HttpVersion
     * @since   2.0.2
     * @deprecated  since 3.0.0, use protocolVersion() instead, will be removed with 4.0.0
     */
    public function getProtocolVersion();

    /**
     * returns the ip which issued the request originally
     *
     * The originating IP address is the IP address of the client which issued
     * the request. In case the request was routed via several proxies it will
     * still return the real client IP, and not the IP address of the last proxy
     * in the chain.
     *
     * Please note that the method relies on the values of REMOTE_ADDR provided
     * by PHP and the X-Forwarded-For header. If none of these is present the
     * return value will be null. Additionally, if the value of these headers
     * does not contain a syntactically correct IP address, the return value
     * will be null.
     *
     * Also, the return value might not neccessarily be an existing IP address
     * nor the real IP address of the client, as it may be spoofed.
     *
     * @return  \stubbles\peer\IpAddress
     * @since   3.0.0
     */
    public function originatingIpAddress();

    /**
     * returns the uri of the request
     *
     * In case the composed uri for this request does not denote a valid HTTP
     * uri a RuntimeException is thrown. If you came this far but the request
     * is for an invalid HTTP uri something is completely wrong.
     *
     * @return  \stubbles\peer\http\HttpUri
     */
    public function uri();

    /**
     * returns the uri of the request
     *
     * @return  \stubbles\peer\http\HttpUri
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
     * @return  \stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function headerErrors();

    /**
     * checks whether a request value from headers is valid or not
     *
     * @param   string  $headerName  name of header
     * @return  \stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateHeader($headerName);

    /**
     * returns request value from headers for filtering or validation
     *
     * @param   string  $headerName  name of header
     * @return  \stubbles\input\ValueReader
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
     * @return  \stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function cookieErrors();

    /**
     * checks whether a request value from cookie is valid or not
     *
     * @param   string  $cookieName  name of cookie
     * @return  \stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateCookie($cookieName);

    /**
     * returns request value from cookies for filtering or validation
     *
     * @param   string  $cookieName  name of cookie
     * @return  \stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readCookie($cookieName);

    /**
     * checks whether a request body is valid or not
     *
     * @return  \stubbles\input\ValueValidator
     * @since   1.3.0
     */
    public function validateBody();

    /**
     * returns error collection for request body
     *
     * @return  \stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function bodyErrors();

    /**
     * returns request body for filtering or validation
     *
     * @return  \stubbles\input\ValueReader
     * @since   1.3.0
     */
    public function readBody();
}
