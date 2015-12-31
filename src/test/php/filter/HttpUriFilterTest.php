<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\HttpUriFilter.
 *
 * @group  filter
 */
class HttpUrlFilterTest extends FilterTest
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
        $this->httpUriFilter = HttpUriFilter::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsUriWithoutPortIfItIsDefaultPort()
    {
        assert(
                $this->httpUriFilter->apply($this->createParam('http://example.org'))
                        ->asString(),
                equals('http://example.org/')
        );
    }



    /**
     * @test
     */
    public function returnsUriWithPortIfItIsNonDefaultPort()
    {
        assert(
                $this->httpUriFilter->apply($this->createParam('http://example.org:45'))
                        ->asString(),
                equals('http://example.org:45/')
        );
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        assertNull($this->httpUriFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsNullForEmptyValue()
    {
        assertNull($this->httpUriFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidUri()
    {
        assertNull($this->httpUriFilter->apply($this->createParam('ftp://foobar.de/')));
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidUri()
    {
        $param = $this->createParam('http://wrong example!');
        $this->httpUriFilter->apply($param);
        assertTrue($param->hasError('HTTP_URI_INCORRECT'));
    }

    /**
     * @test
     */
    public function doesNotPerformDnsCheck()
    {
        assert(
                $this->httpUriFilter->apply($this->createParam('http://doesnotexist.foo/'))
                        ->asString(),
                equals('http://doesnotexist.foo/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assert(
                 $this->readParam(null)
                      ->defaultingTo(HttpUri::fromString('http://example.com/'))
                      ->asHttpUri()
                      ->asString(),
                equals('http://example.com/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asHttpUri();
        assertTrue($this->paramErrors->existForWithId('bar', 'HTTP_URI_MISSING'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asHttpUri();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asHttpUriReturnsValidValue()
    {
        assert(
                $this->readParam('http://example.com/')->asHttpUri()->asString(),
                equals('http://example.com/')
        );

    }
}
