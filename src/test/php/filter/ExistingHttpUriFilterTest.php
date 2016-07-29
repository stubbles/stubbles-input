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
use function bovigo\assert\predicate\isSameAs;
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
     * @test
     */
    public function returnsUriWithoutPortIfItIsDefaultPort()
    {
        assert(
                $this->readParam('http://example.org')->asExistingHttpUri(),
                equals(HttpUri::fromString('http://example.org/'))
        );
    }

    /**
     * @test
     */
    public function returnsUriWithPortIfItIsNonDefaultPort()
    {
        assert(
                $this->readParam('http://example.org:45')->asExistingHttpUri(),
                equals(HttpUri::fromString('http://example.org:45/'))
        );
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        assertNull($this->readParam(null)->asExistingHttpUri());
    }

    /**
     * @test
     */
    public function returnsNullForEmptyValue()
    {
        assertNull($this->readParam('')->asExistingHttpUri());
    }

    /**
     * @test
     */
    public function returnsNullForInvalidUri()
    {
        assertNull($this->readParam('ftp://foobar.de/')->asExistingHttpUri());
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidUri()
    {
        $this->readParam('ftp://foobar.de/')->asExistingHttpUri();
        assertTrue(
                $this->paramErrors->existForWithId('bar', 'HTTP_URI_INCORRECT')
        );
    }

    /**
     * @test
     */
    public function returnsHttpUriIfUriHasDnsRecoed()
    {
        assert(
                $this->readParam('http://stubbles.net/')->asExistingHttpUri(),
                equals(HttpUri::fromString('http://stubbles.net/'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfUriHasNoDnsRecord()
    {
        assertNull(
                $this->readParam('http://doesnotexist')->asExistingHttpUri()
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamIfUriHasNoDnsRecoed()
    {
        $this->readParam('http://doesnotexist')->asExistingHttpUri();
        assertTrue(
                $this->paramErrors->existForWithId('bar', 'HTTP_URI_NOT_AVAILABLE')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $httpUri = HttpUri::fromString('http://example.com/');
        assert(
                $this->readParam(null)
                        ->defaultingTo($httpUri)
                        ->asExistingHttpUri(),
                isSameAs($httpUri)
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
                $this->readParam('http://localhost/')->asExistingHttpUri(),
                equals(HttpUri::fromString('http://localhost/'))
        );
    }
}
