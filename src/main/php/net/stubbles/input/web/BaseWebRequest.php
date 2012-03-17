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
use net\stubbles\input\AbstractRequest;
use net\stubbles\input\Param;
use net\stubbles\input\ParamErrors;
use net\stubbles\input\Params;
use net\stubbles\input\filter\ValueFilter;
use net\stubbles\input\validator\ValueReader;
use net\stubbles\input\validator\ValueValidator;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\peer\MalformedUriException;
use net\stubbles\peer\http\HttpUri;
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
        if (strtoupper(trim($_SERVER['REQUEST_METHOD'])) === \net\stubbles\peer\http\Http::POST) {
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
    public function getMethod()
    {
        return strtoupper($this->headers->getValue('REQUEST_METHOD'));
    }

    /**
     * returns the uri of the request
     *
     * @return  HttpUri
     * @throws  RuntimeException
     */
    public function getUri()
    {
        $uri  = (($this->headers->has('HTTPS')) ? ('https') : ('http')) . '://'
              . $this->headers->getValue('HTTP_HOST')
              . ':' . $this->headers->getValue('SERVER_PORT')
              . $this->headers->getValue('REQUEST_URI');
        try {
            return HttpUri::fromString($uri);
        } catch (MalformedUriException $murie) {
            throw new RuntimeException('Invalid request uri', $murie);
        }
    }

    /**
     * return an array of all header names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function getHeaderNames()
    {
        return $this->headers->getNames();
    }

    /**
     * checks whether a request header is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasHeader($headerName)
    {
        return $this->headers->has($headerName);
    }

    /**
     * returns error collection for request headers
     *
     * @return  net\stubbles\input\ParamErrors
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
     * @return  net\stubbles\input\validator\ValueValidator
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
     * @return  net\stubbles\input\validator\ValueReader
     * @since   1.3.0
     */
    public function readHeader($headerName)
    {
        return new ValueReader($this->headers->get($headerName));
    }

    /**
     * returns request value from headers for filtering or validation
     *
     * @param   string  $headerName  name of header
     * @return  net\stubbles\input\filter\ValueFilter
     * @since   2.0.0
     */
    public function filterHeader($headerName)
    {
        return new ValueFilter($this->headers->errors(),
                               $this->headers->get($headerName)
        );
    }

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  string[]
     * @since   1.3.0
     */
    public function getCookieNames()
    {
        return $this->cookies->getNames();
    }

    /**
     * checks whether a request cookie is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasCookie($cookieName)
    {
        return $this->cookies->has($cookieName);
    }

    /**
     * returns error collection for request cookies
     *
     * @return  net\stubbles\input\ParamErrors
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
     * @return  net\stubbles\input\validator\ValueValidator
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
     * @return  net\stubbles\input\validator\ValueReader
     * @since   1.3.0
     */
    public function readCookie($cookieName)
    {
        return new ValueReader($this->cookies->get($cookieName));
    }

    /**
     * returns request value from cookies for filtering or validation
     *
     * @param   string  $cookieName  name of cookie
     * @return  net\stubbles\input\filter\ValueFilter
     * @since   2.0.0
     */
    public function filterCookie($cookieName)
    {
        return new ValueFilter($this->cookies->errors(),
                               $this->cookies->get($cookieName)
        );
    }

    /**
     * returns error collection for request body
     *
     * @return  ParamErrors
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
     * @return  net\stubbles\input\validator\ValueValidator
     * @since   1.3.0
     */
    public function validateBody()
    {
        return new ValueValidator($this->parseBody());
    }

    /**
     * returns request body for filtering or validation
     *
     * @return  net\stubbles\input\validator\ValueReader
     * @since   1.3.0
     */
    public function readBody()
    {
        return new ValueReader($this->parseBody());
    }

    /**
     * returns request body for filtering or validation
     *
     * @return  net\stubbles\input\filter\ValueFilter
     * @since   2.0.0
     */
    public function filterBody()
    {
        return new ValueFilter($this->bodyErrors(),
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
?>