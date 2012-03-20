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
/**
 * Tests for net\stubbles\input\web\BaseWebRequest.
 *
 * @group  web
 */
class BaseWebRequestTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  BaseWebRequest
     */
    private $baseWebRequest;
    /**
     * backup of globals $_GET, $_POST, $_SERVER, $COOKIE
     *
     * @type array
     */
    private $globals;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->globals        = array('GET'    => $_GET,
                                      'POST'   => $_POST,
                                      'SERVER' => $_SERVER,
                                      'COOKIE' => $_COOKIE

                                );
        $this->baseWebRequest = $this->createBaseWebRequest(array('foo' => 'bar', 'roland' => 'TB-303'),
                                                            array('HTTP_ACCEPT' => 'text/html', 'REQUEST_METHOD' => 'post'),
                                                            array('chocolateChip' => 'Omnomnomnom', 'master' => 'servant')
                                );
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        $_GET    = $this->globals['GET'];
        $_POST   = $this->globals['POST'];
        $_SERVER = $this->globals['SERVER'];
        $_COOKIE = $this->globals['COOKIE'];
    }

    /**
     * creates instance to test
     *
     * @param   array  $params
     * @param   array  $headers
     * @param   array  $cookies
     * @return  BaseWebRequest
     */
    private function createBaseWebRequest(array $params = array(), array $headers = array(), array $cookies = array())
    {
        return new BaseWebRequest($params,
                                  $headers,
                                  $cookies,
                                  function() { return 'request body'; }
        );
    }

    /**
     * helper method to fill globals with data
     *
     * @param  string  $requestMethod
     */
    private function fillGlobals($requestMethod = 'GET')
    {
        $_GET    = array('foo' => 'bar', 'roland' => 'TB-303');
        $_POST   = array('baz' => 'blubb', 'donald' => '313');
        $_SERVER = array('REQUEST_METHOD' => $requestMethod, 'HTTP_ACCEPT' => 'text/html');
        $_COOKIE = array('chocolateChip'  => 'Omnomnomnom', 'master' => 'servant');
    }

    /**
     * @test
     */
    public function usesGetParamsFromRawSourceWhenRequestMethodIsGET()
    {
        $this->fillGlobals('GET');
        $this->assertEquals(array('foo', 'roland'),
                            BaseWebRequest::fromRawSource()->getParamNames()
        );
    }

    /**
     * @test
     */
    public function usesPostParamsFromRawSourceWhenRequestMethodIsPOST()
    {
        $this->fillGlobals('POST');
        $this->assertEquals(array('baz', 'donald'),
                            BaseWebRequest::fromRawSource()->getParamNames()
        );
    }

    /**
     * @test
     */
    public function usesServerForHeaderFromRawSource()
    {
        $this->fillGlobals();
        $this->assertEquals(array('REQUEST_METHOD', 'HTTP_ACCEPT'),
                            BaseWebRequest::fromRawSource()->getHeaderNames()
        );
    }

    /**
     * @test
     */
    public function usesCookieForCookieFromRawSource()
    {
        $this->fillGlobals();
        $this->assertEquals(array('chocolateChip', 'master'),
                            BaseWebRequest::fromRawSource()->getCookieNames()
        );
    }

    /**
     * @test
     */
    public function usesPhpInputForCookieFromRawSource()
    {
        $this->fillGlobals();
        $this->assertEquals('',
                            BaseWebRequest::fromRawSource()->readBody()->unsecure()
        );
    }

    /**
     * @test
     */
    public function returnsRequestMethodInUpperCase()
    {
        $this->assertEquals('POST', $this->baseWebRequest->getMethod());
    }

    /**
     * @test
     */
    public function sslCheckReturnsTrueIfHttpsSet()
    {
        $this->assertTrue($this->createBaseWebRequest(array(),
                                                      array('HTTPS' => true)
                                 )
                               ->isSsl()
        );
    }

    /**
     * @test
     */
    public function sslCheckReturnsFalseIfHttpsNotSet()
    {
        $this->assertFalse($this->createBaseWebRequest(array(),
                                                       array('HTTPS' => null)
                                  )
                                ->isSsl()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function getUriThrowsRuntimeExceptionOnInvalidRequestUri()
    {
        $this->createBaseWebRequest(array(),
                                    array()
               )
             ->getUri();
    }

    /**
     * @test
     */
    public function getUriReturnsCompleteRequestUri()
    {
        $this->assertEquals('http://stubbles.net:80/index.php?foo=bar',
                            $this->createBaseWebRequest(array('foo'         => 'bar'),
                                                        array('HTTPS'       => null,
                                                              'HTTP_HOST'   => 'stubbles.net',
                                                              'SERVER_PORT' => 80,
                                                              'REQUEST_URI' => '/index.php?foo=bar'
                                                        )
                                   )
                                 ->getUri()
                                 ->asString()
        );
    }

    /**
     * @test
     */
    public function getUriReturnsCompleteRequestUriForHttps()
    {
        $this->assertEquals('https://stubbles.net:443/index.php?foo=bar',
                            $this->createBaseWebRequest(array('foo'         => 'bar'),
                                                        array('HTTPS'       => true,
                                                              'HTTP_HOST'   => 'stubbles.net',
                                                              'SERVER_PORT' => 443,
                                                              'REQUEST_URI' => '/index.php?foo=bar'
                                                        )
                                   )
                                 ->getUri()
                                 ->asString()
        );
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        $this->assertEquals(array('foo', 'roland'),
                            $this->baseWebRequest->getParamNames()
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ParamErrors',
                                $this->baseWebRequest->paramErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingParam()
    {
        $this->assertFalse($this->baseWebRequest->hasParam('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingParam()
    {
        $this->assertTrue($this->baseWebRequest->hasParam('foo'));
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidator()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->baseWebRequest->validateParam('foo')
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->baseWebRequest->validateParam('baz')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReader()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueReader',
                                $this->baseWebRequest->readParam('foo')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueReader',
                                $this->baseWebRequest->readParam('baz')
        );
    }

    /**
     * @test
     */
    public function filterParamReturnsValueFilter()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->baseWebRequest->filterParam('foo')
        );
    }

    /**
     * @test
     */
    public function filterParamReturnsValueFilterForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->baseWebRequest->filterParam('baz')
        );
    }

    /**
     * @test
     */
    public function returnsListOfHeaderNames()
    {
        $this->assertEquals(array('HTTP_ACCEPT', 'REQUEST_METHOD'),
                            $this->baseWebRequest->getHeaderNames()
        );
    }

    /**
     * @test
     */
    public function returnsHeaderErrors()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ParamErrors',
                                $this->baseWebRequest->headerErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingHeader()
    {
        $this->assertFalse($this->baseWebRequest->hasHeader('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingHeader()
    {
        $this->assertTrue($this->baseWebRequest->hasHeader('HTTP_ACCEPT'));
    }

    /**
     * @test
     */
    public function validateHeaderReturnsValueValidator()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->baseWebRequest->validateHeader('HTTP_ACCEPT')
        );
    }

    /**
     * @test
     */
    public function validateHeaderReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->baseWebRequest->validateHeader('baz')
        );
    }

    /**
     * @test
     */
    public function readHeaderReturnsValueReader()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueReader',
                                $this->baseWebRequest->readHeader('HTTP_ACCEPT')
        );
    }

    /**
     * @test
     */
    public function readHeaderReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueReader',
                                $this->baseWebRequest->readHeader('baz')
        );
    }

    /**
     * @test
     */
    public function filterHeaderReturnsValueFilter()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->baseWebRequest->filterHeader('HTTP_ACCEPT')
        );
    }

    /**
     * @test
     */
    public function filterHeaderReturnsValueFilterForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->baseWebRequest->filterHeader('baz')
        );
    }

    /**
     * @test
     */
    public function returnsListOfCookieNames()
    {
        $this->assertEquals(array('chocolateChip', 'master'),
                            $this->baseWebRequest->getCookieNames()
        );
    }

    /**
     * @test
     */
    public function returnsCookieErrors()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ParamErrors',
                                $this->baseWebRequest->cookieErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingCookie()
    {
        $this->assertFalse($this->baseWebRequest->hasHeader('baz'));
    }

    /**
     * @test
     */
    public function returnsTrueOnCheckForExistingCookie()
    {
        $this->assertTrue($this->baseWebRequest->hasCookie('chocolateChip'));
    }

    /**
     * @test
     */
    public function validateCookieReturnsValueValidator()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->baseWebRequest->validateCookie('chocolateChip')
        );
    }

    /**
     * @test
     */
    public function validateCookieReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->baseWebRequest->validateCookie('baz')
        );
    }

    /**
     * @test
     */
    public function readCookieReturnsValueReader()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueReader',
                                $this->baseWebRequest->readCookie('chocolateChip')
        );
    }

    /**
     * @test
     */
    public function readCookieReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueReader',
                                $this->baseWebRequest->readCookie('baz')
        );
    }

    /**
     * @test
     */
    public function filterCookieReturnsValueFilter()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->baseWebRequest->filterCookie('chocolateChip')
        );
    }

    /**
     * @test
     */
    public function filterCookieReturnsValueFilterForNonExistingParam()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->baseWebRequest->filterCookie('baz')
        );
    }



    /**
     * @test
     */
    public function returnsBodyErrors()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ParamErrors',
                                $this->baseWebRequest->bodyErrors()
        );
    }

    /**
     * @test
     */
    public function validateBodyReturnsValueValidator()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueValidator',
                                $this->baseWebRequest->validateBody()
        );
    }

    /**
     * @test
     */
    public function readBodyReturnsValueReader()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\validator\\ValueReader',
                                $this->baseWebRequest->readBody()
        );
    }

    /**
     * @test
     */
    public function bodyIsParsedFromGivenBodyParserFunction()
    {
        $this->assertEquals('request body',
                            $this->baseWebRequest->readBody()->unsecure()
        );
    }

    /**
     * @test
     */
    public function filterBodyReturnsValueFilter()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                $this->baseWebRequest->filterBody()
        );
    }
}
?>