<?php
declare(strict_types=1);
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
        assert(
                $this->existinghttpUriFilter->apply($this->createParam('http://example.org'))
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
                $this->existinghttpUriFilter->apply($this->createParam('http://example.org:45'))
                        ->asString(),
                equals('http://example.org:45/')
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
        assert(
                $this->existinghttpUriFilter->apply($this->createParam('http://stubbles.net/'))
                        ->asString(),
                equals('http://stubbles.net/')
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
        assert(
                $this->readParam(null)
                     ->defaultingTo(HttpUri::fromString('http://example.com/'))
                     ->asExistingHttpUri()
                     ->asString(),
                equals('http://example.com/')
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
        assert(
                $this->readParam('http://localhost/')
                     ->asExistingHttpUri()
                     ->asString(),
                equals('http://localhost/')
        );
    }
}
