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
use stubbles\peer\http\HttpVersion;
/**
 * Tests for stubbles\input\web\BaseWebRequest.
 *
 * @group  web
 */
class BaseWebRequestTest extends \PHPUnit_Framework_TestCase
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
        $this->globals        = ['GET'    => $_GET,
                                 'POST'   => $_POST,
                                 'SERVER' => $_SERVER,
                                 'COOKIE' => $_COOKIE

                                ];
        $this->baseWebRequest = $this->createBaseWebRequest(['foo' => 'bar', 'roland' => 'TB-303'],
                                                            ['HTTP_ACCEPT' => 'text/html', 'REQUEST_METHOD' => 'post'],
                                                            ['chocolateChip' => 'Omnomnomnom', 'master' => 'servant']
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
     * @param   array  $params   optional
     * @param   array  $headers  optional
     * @param   array  $cookies  optional
     * @return  BaseWebRequest
     */
    private function createBaseWebRequest(array $params = [], array $headers = [], array $cookies = [])
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
        $_GET    = ['foo' => 'bar', 'roland' => 'TB-303'];
        $_POST   = ['baz' => 'blubb', 'donald' => '313'];
        $_SERVER = ['REQUEST_METHOD' => $requestMethod, 'HTTP_ACCEPT' => 'text/html'];
        $_COOKIE = ['chocolateChip'  => 'Omnomnomnom', 'master' => 'servant'];
    }

    /**
     * @test
     */
    public function usesGetParamsFromRawSourceWhenRequestMethodIsGET()
    {
        $this->fillGlobals('GET');
        $this->assertEquals(['foo', 'roland'],
                            BaseWebRequest::fromRawSource()->paramNames()
        );
    }

    /**
     * @test
     */
    public function usesPostParamsFromRawSourceWhenRequestMethodIsPOST()
    {
        $this->fillGlobals('POST');
        $this->assertEquals(['baz', 'donald'],
                            BaseWebRequest::fromRawSource()->paramNames()
        );
    }

    /**
     * @test
     */
    public function usesServerForHeaderFromRawSource()
    {
        $this->fillGlobals();
        $this->assertEquals(['REQUEST_METHOD', 'HTTP_ACCEPT'],
                            BaseWebRequest::fromRawSource()->headerNames()
        );
    }

    /**
     * @test
     */
    public function usesCookieForCookieFromRawSource()
    {
        $this->fillGlobals();
        $this->assertEquals(['chocolateChip', 'master'],
                            BaseWebRequest::fromRawSource()->cookieNames()
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
        $this->assertEquals('POST', $this->baseWebRequest->method());
    }

    /**
     * @test
     */
    public function sslCheckReturnsTrueIfHttpsSet()
    {
        $this->assertTrue($this->createBaseWebRequest([],
                                                      ['HTTPS' => true]
                                 )
                               ->isSsl()
        );
    }

    /**
     * @test
     */
    public function sslCheckReturnsFalseIfHttpsNotSet()
    {
        $this->assertFalse($this->createBaseWebRequest([],
                                                       ['HTTPS' => null]
                                  )
                                ->isSsl()
        );
    }

    /**
     * @since  2.0.2
     * @test
     */
    public function reportsVersion1_0WhenNoServerProtocolSet()
    {
         $this->assertEquals(HttpVersion::HTTP_1_0,
                             $this->createBaseWebRequest([], [])
                                  ->protocolVersion()
        );
    }

    /**
     * @since  2.0.2
     * @test
     */
    public function reportsNullWhenServerProtocolContainsInvalidVersion()
    {
         $this->assertNull($this->createBaseWebRequest([],
                                                       ['SERVER_PROTOCOL' => 'foo']
                                  )
                                ->protocolVersion()
        );
    }

    /**
     * @return  array
     */
    public function protocolVersions()
    {
        return [
            ['HTTP/0.9', '0.9'],
            ['HTTP/1.0', '1.0'],
            ['HTTP/1.1', '1.1'],
            ['HTTP/1.2', '1.2'],
            ['HTTP/1.12', '1.12'],
            ['HTTP/2.0', '2.0'],
        ];
    }

    /**
     * @since  3.0.0
     * @test
     * @dataProvider  protocolVersions
     */
    public function reportsParsedProtocolVersion($protocol)
    {
         $this->assertEquals(
                 HttpVersion::fromString($protocol),
                 $this->createBaseWebRequest([], ['SERVER_PROTOCOL' => $protocol])
                      ->protocolVersion()
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function originatingIpAdressIsNullWhenAccordingHeadersNotPresent()
    {
        $this->assertNull($this->createBaseWebRequest()->originatingIpAddress());
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function originatingIpAddressIsRemoteAddressWhenNoForwardedForHeaderPresent()
    {
        $this->assertEquals(
                '127.0.0.1',
                $this->createBaseWebRequest([], ['REMOTE_ADDR' => '127.0.0.1'])
                     ->originatingIpAddress()
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function originatingIpAddressIsForwardedAddressWhenForwardedForHeaderPresent()
    {
        $this->assertEquals(
                '172.19.120.122',
                $this->createBaseWebRequest(
                        [],
                        ['REMOTE_ADDR'          => '127.0.0.1',
                         'HTTP_X_FORWARDED_FOR' => '172.19.120.122'
                        ]
                       )
                     ->originatingIpAddress()
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function originatingIpAddressIsFirstFromForwardedAddressesWhenForwardedForHeaderContainsList()
    {
        $this->assertEquals(
                '172.19.120.122',
                $this->createBaseWebRequest(
                        [],
                        ['REMOTE_ADDR'          => '127.0.0.1',
                         'HTTP_X_FORWARDED_FOR' => '172.19.120.122, 168.30.48.124'
                        ]
                       )
                     ->originatingIpAddress()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
     */
    public function getUriThrowsRuntimeExceptionOnInvalidRequestUri()
    {
        $this->createBaseWebRequest([], [])->uri();
    }

    /**
     * @test
     */
    public function getUriReturnsCompleteRequestUri()
    {
        $this->assertEquals('http://stubbles.net:80/index.php?foo=bar',
                            $this->createBaseWebRequest(['foo'         => 'bar'],
                                                        ['HTTPS'       => null,
                                                         'HTTP_HOST'   => 'stubbles.net',
                                                         'SERVER_PORT' => 80,
                                                         'REQUEST_URI' => '/index.php?foo=bar'
                                                        ]
                                   )
                                 ->uri()
                                 ->asString()
        );
    }

    /**
     * @test
     * @since  2.3.2
     */
    public function getUriReturnsCompleteRequestUriWithoutDoublePortIfPortIsInHost()
    {
        $this->assertEquals('http://localhost:8080/index.php?foo=bar',
                            $this->createBaseWebRequest(['foo'         => 'bar'],
                                                        ['HTTPS'       => null,
                                                         'HTTP_HOST'   => 'localhost:8080',
                                                         'SERVER_PORT' => 80,
                                                         'REQUEST_URI' => '/index.php?foo=bar'
                                                        ]
                                   )
                                 ->uri()
                                 ->asString()
        );
    }

    /**
     * @test
     * @since  2.3.2
     */
    public function getUriReturnsCompleteRequestUriWithNonDefaultPort()
    {
        $this->assertEquals('http://example.net:8080/index.php?foo=bar',
                            $this->createBaseWebRequest(['foo'         => 'bar'],
                                                        ['HTTPS'       => null,
                                                         'HTTP_HOST'   => 'example.net',
                                                         'SERVER_PORT' => 8080,
                                                         'REQUEST_URI' => '/index.php?foo=bar'
                                                        ]
                                   )
                                 ->uri()
                                 ->asString()
        );
    }

    /**
     * @test
     */
    public function getUriReturnsCompleteRequestUriForHttps()
    {
        $this->assertEquals('https://stubbles.net:443/index.php?foo=bar',
                            $this->createBaseWebRequest(['foo'         => 'bar'],
                                                        ['HTTPS'       => true,
                                                         'HTTP_HOST'   => 'stubbles.net',
                                                         'SERVER_PORT' => 443,
                                                         'REQUEST_URI' => '/index.php?foo=bar'
                                                        ]
                                   )
                                 ->uri()
                                 ->asString()
        );
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        $this->assertEquals(['foo', 'roland'],
                            $this->baseWebRequest->paramNames()
        );
    }

    /**
     * @test
     */
    public function returnsParamErrors()
    {
        $this->assertInstanceOf('stubbles\input\errors\ParamErrors',
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
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->baseWebRequest->validateParam('foo')
        );
    }

    /**
     * @test
     */
    public function validateParamReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->baseWebRequest->validateParam('baz')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReader()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->baseWebRequest->readParam('foo')
        );
    }

    /**
     * @test
     */
    public function readParamReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->baseWebRequest->readParam('baz')
        );
    }

    /**
     * @test
     */
    public function returnsListOfHeaderNames()
    {
        $this->assertEquals(['HTTP_ACCEPT', 'REQUEST_METHOD'],
                            $this->baseWebRequest->headerNames()
        );
    }

    /**
     * @test
     */
    public function returnsHeaderErrors()
    {
        $this->assertInstanceOf('stubbles\input\errors\ParamErrors',
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
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->baseWebRequest->validateHeader('HTTP_ACCEPT')
        );
    }

    /**
     * @test
     */
    public function validateHeaderReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->baseWebRequest->validateHeader('baz')
        );
    }

    /**
     * @test
     */
    public function readHeaderReturnsValueReader()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->baseWebRequest->readHeader('HTTP_ACCEPT')
        );
    }

    /**
     * @test
     */
    public function readHeaderReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->baseWebRequest->readHeader('baz')
        );
    }

    /**
     * @test
     */
    public function returnsListOfCookieNames()
    {
        $this->assertEquals(['chocolateChip', 'master'],
                            $this->baseWebRequest->cookieNames()
        );
    }

    /**
     * @test
     */
    public function returnsCookieErrors()
    {
        $this->assertInstanceOf('stubbles\input\errors\ParamErrors',
                                $this->baseWebRequest->cookieErrors()
        );
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingCookie()
    {
        $this->assertFalse($this->baseWebRequest->hasCookie('baz'));
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
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->baseWebRequest->validateCookie('chocolateChip')
        );
    }

    /**
     * @test
     */
    public function validateCookieReturnsValueValidatorForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->baseWebRequest->validateCookie('baz')
        );
    }

    /**
     * @test
     */
    public function readCookieReturnsValueReader()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->baseWebRequest->readCookie('chocolateChip')
        );
    }

    /**
     * @test
     */
    public function readCookieReturnsValueReaderForNonExistingParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                $this->baseWebRequest->readCookie('baz')
        );
    }

    /**
     * @test
     */
    public function returnsBodyErrors()
    {
        $this->assertInstanceOf('stubbles\input\errors\ParamErrors',
                                $this->baseWebRequest->bodyErrors()
        );
    }

    /**
     * @test
     */
    public function validateBodyReturnsValueValidator()
    {
        $this->assertInstanceOf('stubbles\input\ValueValidator',
                                $this->baseWebRequest->validateBody()
        );
    }

    /**
     * @test
     */
    public function readBodyReturnsValueReader()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
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
}
