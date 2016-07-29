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
use bovigo\callmap\NewCallable;
use stubbles\peer\http\HttpUri;

use function bovigo\callmap\verify;
use function bovigo\assert\{
    assert,
    assertNull,
    assertTrue,
    predicate\equals,
    predicate\isSameAs
};
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\ExistingHttpUriFilter.
 *
 * @group  filter
 * @since  3.0.0
 */
class ExistingHttpUrlFilterTest extends FilterTest
{
    private $checkdnsrr;

    public function setUp()
    {
        $this->checkdnsrr = NewCallable::stub('checkdnsrr')->mapCall(false);
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsUriWithoutPortIfItIsDefaultPort()
    {
        $this->checkdnsrr->mapCall(true);
        assert(
                $this->readParam('http://example.org')->asExistingHttpUri($this->checkdnsrr),
                equals(HttpUri::fromString('http://example.org/'))
        );
    }

    /**
     * @test
     */
    public function returnsUriWithPortIfItIsNonDefaultPort()
    {
        $this->checkdnsrr->mapCall(true);
        assert(
                $this->readParam('http://example.org:45')->asExistingHttpUri($this->checkdnsrr),
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
        assertNull($this->readParam('ftp://foobar.de/')->asExistingHttpUri($this->checkdnsrr));
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidUri()
    {
        $this->readParam('ftp://foobar.de/')->asExistingHttpUri($this->checkdnsrr);
        assertTrue(
                $this->paramErrors->existForWithId('bar', 'HTTP_URI_INCORRECT')
        );
    }

    /**
     * @test
     */
    public function doesNotActuallyCheckDnsRecordWhenUriIsInvalidAnyway()
    {
        $this->readParam('ftp://foobar.de/')->asExistingHttpUri($this->checkdnsrr);
        verify($this->checkdnsrr)->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsHttpUriIfUriHasDnsRecoed()
    {
        $this->checkdnsrr->mapCall(true);
        assert(
                $this->readParam('http://stubbles.net/')->asExistingHttpUri($this->checkdnsrr),
                equals(HttpUri::fromString('http://stubbles.net/'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfUriHasNoDnsRecord()
    {
        assertNull(
                $this->readParam('http://doesnotexist')
                        ->asExistingHttpUri($this->checkdnsrr)
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamIfUriHasNoDnsRecoed()
    {
        $this->readParam('http://doesnotexist')->asExistingHttpUri($this->checkdnsrr);
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
}
