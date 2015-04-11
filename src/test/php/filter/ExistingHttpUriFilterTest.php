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
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\ExistingHttpUriFilter.
 *
 * @group  filter
 * @since  3.0.0
 */
class ExistingHttpUrlFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  ExistingHttpUriFilter
     */
    private $existinghttpUriFilter;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->existinghttpUriFilter = ExistingHttpUriFilter::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsUriWithoutPortIfItIsDefaultPort()
    {
        assertEquals(
                'http://example.org/',
                $this->existinghttpUriFilter->apply($this->createParam('http://example.org'))
                        ->asString()
        );
    }



    /**
     * @test
     */
    public function returnsUriWithPortIfItIsNonDefaultPort()
    {
        assertEquals(
                'http://example.org:45/',
                $this->existinghttpUriFilter->apply($this->createParam('http://example.org:45'))
                        ->asString()
        );
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        assertNull($this->existinghttpUriFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsNullForEmptyValue()
    {
        assertNull($this->existinghttpUriFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidUri()
    {
        assertNull(
                $this->existinghttpUriFilter->apply(
                        $this->createParam('ftp://foobar.de/')
                )
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidUri()
    {
        $param = $this->createParam('http://wrong example!');
        $this->existinghttpUriFilter->apply($param);
        assertTrue($param->hasError('HTTP_URI_INCORRECT'));
    }

    /**
     * @test
     */
    public function returnsHttpUriIfUriHasDnsRecoed()
    {
        assertEquals(
                'http://stubbles.net/',
                $this->existinghttpUriFilter->apply($this->createParam('http://stubbles.net/'))
                        ->asString()
        );
    }

    /**
     * @test
     */
    public function returnsNullIfUriHasNoDnsRecord()
    {
        assertNull(
                $this->existinghttpUriFilter->apply(
                        $this->createParam('http://doesnotexist.1und1.de/')
                )
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamIfUriHasNoDnsRecoed()
    {
        $param = $this->createParam('http://doesnotexist/');
        $this->existinghttpUriFilter->apply($param);
        assertTrue($param->hasError('HTTP_URI_NOT_AVAILABLE'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assertEquals(
                'http://example.com/',
                $this->readParam(null)
                     ->defaultingTo(HttpUri::fromString('http://example.com/'))
                     ->asExistingHttpUri()
                     ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asExistingHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asExistingHttpUri();
        assertTrue($this->paramErrors->existForWithId('bar', 'HTTP_URI_MISSING'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asExistingHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asExistingHttpUri();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsValidValue()
    {
        assertEquals(
                'http://localhost/',
                $this->readParam('http://localhost/')
                     ->asExistingHttpUri()
                     ->asString()
        );
    }
}
