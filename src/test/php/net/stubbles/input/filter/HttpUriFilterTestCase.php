<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\HttpUriFilter.
 *
 * @package  filter
 */
class HttpUrlFilterTestCase extends FilterTestCase
{
    /**
     * instance to test
     *
     * @type  HttpUriFilter
     */
    private $httpUriFilter;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->httpUriFilter = new HttpUriFilter();
    }

    /**
     * @test
     */
    public function returnsUriWithoutPortIfItIsDefaultPort()
    {
        $this->assertEquals('http://example.org/',
                            $this->httpUriFilter->apply($this->createParam('http://example.org'))
                                                ->asString()
        );
    }



    /**
     * @test
     */
    public function returnsUriWithPortIfItIsNonDefaultPort()
    {
        $this->assertEquals('http://example.org:45/',
                            $this->httpUriFilter->apply($this->createParam('http://example.org:45'))
                                                ->asString()
        );
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        $this->assertNull($this->httpUriFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsNullForEmptyValue()
    {
        $this->assertNull($this->httpUriFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidUri()
    {
        $this->assertNull($this->httpUriFilter->apply($this->createParam('ftp://foobar.de/')));
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidUri()
    {
        $param = $this->createParam('http://wrong example!');
        $this->httpUriFilter->apply($param);
        $this->assertTrue($param->hasError('HTTP_URI_INCORRECT'));
    }

    /**
     * @test
     */
    public function dnsCheckIsDisabledByDefault()
    {
        $this->assertEquals('http://doesnotexist.foo/',
                            $this->httpUriFilter->apply($this->createParam('http://doesnotexist.foo/'))
                                                ->asString()
        );
    }

    /**
     * @test
     */
    public function dnsCheckEnabledReturnsHttpUriIfUriHasDnsRecoed()
    {
        $this->assertEquals('http://stubbles.net/',
                            $this->httpUriFilter->enforceDnsRecord()
                                                ->apply($this->createParam('http://stubbles.net/'))
                                                ->asString()
        );
    }

    /**
     * @test
     */
    public function dnsCheckEnabledReturnsNullIfUriHasNoDnsRecoed()
    {
        $this->httpUriFilter->enforceDnsRecord()
                            ->apply($this->createParam('http://doesnotexist.1und1.de/'));
    }

    /**
     * @test
     */
    public function dnsCheckEnabledAddsErrorToParamIfUriHasNoDnsRecoed()
    {
        $param = $this->createParam('http://doesnotexist.foo/');
        $this->httpUriFilter->enforceDnsRecord()
                            ->apply($param);
        $this->assertTrue($param->hasError('HTTP_URI_NOT_AVAILABLE'));
    }
}
?>