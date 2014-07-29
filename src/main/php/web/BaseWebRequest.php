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
use stubbles\input\AbstractRequest;
use stubbles\input\Param;
use stubbles\input\Params;
use stubbles\input\ValueReader;
use stubbles\input\ValueValidator;
use stubbles\input\errors\ParamErrors;
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\RuntimeException;
use stubbles\peer\MalformedUriException;
use stubbles\peer\http\Http;
use stubbles\peer\http\HttpUri;
use stubbles\peer\http\HttpVersion;
/**
 * Request implementation for web applications.
 */
class BaseWebRequest extends AbstractRequest implements WebRequest
{
    /**
     * list of params
     *
     * @type  Params
     */
    private $headers;
    /**
     * list of params
     *
     * @type  Params
     */
    private $cookies;
    /**
     * list of body errors
     *
     * @type  BodyErrors
     */
    private $bodyErrors;
    /**
     * reader for request body
     *
     * @type  \Closure
     */
    private $bodyParser;

    /**
     * constructor
     *
     * @param  array     $params      map of request parameters
     * @param  array     $headers     map of request headers
     * @param  array     $cookies     map of request cookies
     * @param  \Closure  $bodyParser  function which returns the request body
     */
    public function __construct(array $params, array $headers, array $cookies, \Closure $bodyParser)
    {
        parent::__construct(new Params($params));
        $this->headers    = new Params($headers);
        $this->cookies    = new Params($cookies);
        $this->bodyParser = $bodyParser;
    }

    /**
     * creates an instance from raw data, meaning $_GET/$_POST, $_SERVER and $_COOKIE
     *
     * @api
     * @return  WebRequest
     */
    public static function fromRawSource()
    {
        if (strtoupper(trim($_SERVER['REQUEST_METHOD'])) === \stubbles\peer\http\Http::POST) {
            $params = $_POST;
        } else {
            $params = $_GET;
        }

        return new self($params,
                        $_SERVER,
                        $_COOKIE,
                        function()
                        {
                            return file_get_contents('php://input');
                        }
        );
    }

    /**
     * returns the request method
     *
     * @return  string
     */
    public function method()
    {
        return strtoupper($this->headers->value('REQUEST_METHOD'));
    }

    /**
     * returns the request method
     *
     * @return  string
     * @deprecated  since 3.0.0, use method() instead, will be removed with 4.0.0
     */
    public function getMethod()
    {
        return $this->method();
    }

    /**
     * checks whether request was made using ssl
     *
     * @return  bool
     */
    public function isSsl()
    {
        return $this->headers->contain('HTTPS');
    }

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
    public function protocolVersion()
    {
        if (!$this->headers->contain('SERVER_PROTOCOL')) {
            return new HttpVersion(1, 0);
        }

        try {
            return HttpVersion::fromString($this->headers->value('SERVER_PROTOCOL'));
        } catch (IllegalArgumentException $ex) {
            return null;
        }
    }

    /**
     * returns HTTP protocol version of request
     *
     * In case the version is not HTTP/1.0 or HTTP/1.1 return value is <null>.
     *
     * @return  \stubbles\peer\http\HttpVersion
     * @since   2.0.2
     * @deprecated  since 3.0.0, use protocolVersion() instead, will be removed with 4.0.0
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion();
    }

    /**
     * returns the ip address which issued the request originally
     *
     * The originating IP address is the IP address of the client which issued
     * the request. In case the request was routed via several proxies it will
     * still return the real client's IP, and not the IP address of the last
     * proxy in the chain.
     *
     * Please note that the method relies on the values of REMOTE_ADDR provided
     * by PHP and the X-Forwarded-For header. If none of these is present the
     * return value will be null.
     *
     * Also, the return value might not neccessarily be a valid IP address nor
     * the real IP address of the client, as it may be spoofed. You should check
     * the return value for validity. We don't check the value here so callers
     * can use the raw value.
     *
     * @return  string
     * @since   3.0.0
     */
    public function originatingIpAddress()
    {
        if ($this->headers->contain('HTTP_X_FORWARDED_FOR')) {
            $remoteAddresses = explode(',', $this->headers->value('HTTP_X_FORWARDED_FOR'));
            return trim($remoteAddresses[0]);
        }

        if ($this->headers->contain('REMOTE_ADDR')) {
            return $this->headers->value('REMOTE_ADDR');
        }

        return null;
    }

    /**
     * returns the uri of the request
     *
     * In case the composed uri for this request does not denote a valid HTTP
     * uri a RuntimeException is thrown. If you came this far but the request
     * is for an invalid HTTP uri something is completely wrong.
     *
     * @return  HttpUri
     * @throws  RuntimeException
     */
    public function uri()
    {
        $host = $this->headers->value('HTTP_HOST');
        try {
            return HttpUri::fromParts(
                    (($this->headers->contain('HTTPS')) ? (Http::SCHEME_SSL) : (Http::SCHEME)),
                    $host,
                    (strstr($host, ':') === false ? $this->headers->value('SERVER_PORT') : null),
                    $this->headers->value('REQUEST_URI') // already contains query string
            );
        } catch (MalformedUriException $murie) {
            throw new RuntimeException('Invalid request uri', $murie);
        }
    }

    /**
     * returns the uri of the request
     *
     * @return  HttpUri
     * @throws  RuntimeException
     * @deprecated  since 3.0.0, use uri() instead, will be removed with 4.0.0
     */
    public function getUri()
    {
        return $this->uri();
    }

    /**
     * return an array of all header names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function headerNames()
    {
        return $this->headers->names();
    }

    /**
     * return an array of all header names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     * @deprecated  since 3.0.0, use headerNames() instead, will be removed with 4.0.0
     */
    public function getHeaderNames()
    {
        return $this->headersNames();
    }

    /**
     * checks whether a request header is set
     *
     * @param   string  $headerName
     * @return  bool
     * @since   1.3.0
     */
    public function hasHeader($headerName)
    {
        return $this->headers->contain($headerName);
    }

    /**
     * returns error collection for request headers
     *
     * @return  stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function headerErrors()
    {
        return $this->headers->errors();
    }

    /**
     * checks whether a request value from headers is valid or not
     *
     * @param   string  $headerName  name of header
     * @return  ValueValidator
     * @since   1.3.0
     */
    public function validateHeader($headerName)
    {
        return new ValueValidator($this->headers->get($headerName));
    }

    /**
     * returns request value from headers for filtering or validation
     *
     * @param   string  $headerName  name of header
     * @return  ValueReader
     * @since   1.3.0
     */
    public function readHeader($headerName)
    {
        return new ValueReader($this->headers->errors(),
                               $this->headers->get($headerName)
        );
    }

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function cookieNames()
    {
        return $this->cookies->names();
    }

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     * @deprecated  since 3.0.0, use cookieNames() instead, will be removed with 4.0.0
     */
    public function getCookieNames()
    {
        return $this->cookieNames();
    }

    /**
     * checks whether a request cookie is set
     *
     * @param   string  $cookieName
     * @return  bool
     * @since   1.3.0
     */
    public function hasCookie($cookieName)
    {
        return $this->cookies->contain($cookieName);
    }

    /**
     * returns error collection for request cookies
     *
     * @return  stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function cookieErrors()
    {
        return $this->cookies->errors();
    }

    /**
     * checks whether a request value from cookie is valid or not
     *
     * @param   string  $cookieName  name of cookie
     * @return  ValueValidator
     * @since   1.3.0
     */
    public function validateCookie($cookieName)
    {
        return new ValueValidator($this->cookies->get($cookieName));
    }

    /**
     * returns request value from cookies for filtering or validation
     *
     * @param   string  $cookieName  name of cookie
     * @return  ValueReader
     * @since   1.3.0
     */
    public function readCookie($cookieName)
    {
        return new ValueReader($this->cookies->errors(),
                               $this->cookies->get($cookieName)
        );
    }

    /**
     * returns error collection for request body
     *
     * @return  stubbles\input\errors\ParamErrors
     * @since   1.3.0
     */
    public function bodyErrors()
    {
        if (null === $this->bodyErrors) {
            $this->bodyErrors = new ParamErrors();
        }

        return $this->bodyErrors;
    }

    /**
     * checks whether a request body is valid or not
     *
     * @return  ValueValidator
     * @since   1.3.0
     */
    public function validateBody()
    {
        return new ValueValidator($this->parseBody());
    }

    /**
     * returns request body for filtering or validation
     *
     * @return  ValueReader
     * @since   1.3.0
     */
    public function readBody()
    {
        return new ValueReader($this->bodyErrors(),
                               $this->parseBody()
        );
    }

    /**
     * read request body
     *
     * @return  Param
     */
    private function parseBody()
    {
        $bodyParser = $this->bodyParser;
        return new Param('body', $bodyParser());
    }
}
