<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use bovigo\callmap\NewCallable;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\peer\http\HttpUri;

use function bovigo\callmap\verify;
use function bovigo\assert\{
    assertThat,
    assertNull,
    assertTrue,
    predicate\equals,
    predicate\isSameAs
};
/**
 * Tests for stubbles\input\filter\ExistingHttpUriFilter.
 *
 * @since  3.0.0
 */
#[Group('filter')]
class ExistingHttpUriFilterTest extends FilterTestBase
{
    /**
     * @var  callable&\bovigo\callmap\FunctionProxy
     */
    private $checkdnsrr;

    protected function setUp(): void
    {
        $this->checkdnsrr = NewCallable::stub('checkdnsrr')->returns(false);
        parent::setUp();
    }

    #[Test]
    public function returnsUriWithoutPortIfItIsDefaultPort(): void
    {
        $this->checkdnsrr->returns(true);
        assertThat(
            $this->readParam('http://example.org')->asExistingHttpUri($this->checkdnsrr),
            equals(HttpUri::fromString('http://example.org/'))
        );
    }

    #[Test]
    public function returnsUriWithPortIfItIsNonDefaultPort(): void
    {
        $this->checkdnsrr->returns(true);
        assertThat(
            $this->readParam('http://example.org:45')->asExistingHttpUri($this->checkdnsrr),
            equals(HttpUri::fromString('http://example.org:45/'))
        );
    }

    #[Test]
    public function returnsNullForNull(): void
    {
        assertNull($this->readParam(null)->asExistingHttpUri());
    }

    #[Test]
    public function returnsNullForEmptyValue(): void
    {
        assertNull($this->readParam('')->asExistingHttpUri());
    }

    #[Test]
    public function returnsNullForInvalidUri(): void
    {
        assertNull(
            $this->readParam('ftp://foobar.de/')->asExistingHttpUri($this->checkdnsrr)
        );
    }

    #[Test]
    public function addsErrorToParamForInvalidUri(): void
    {
        $this->readParam('ftp://foobar.de/')->asExistingHttpUri($this->checkdnsrr);
        assertTrue(
                $this->paramErrors->existForWithId('bar', 'HTTP_URI_INCORRECT')
        );
    }

    #[Test]
    public function doesNotActuallyCheckDnsRecordWhenUriIsInvalidAnyway(): void
    {
        $this->readParam('ftp://foobar.de/')->asExistingHttpUri($this->checkdnsrr);
        assertTrue(verify($this->checkdnsrr)->wasNeverCalled());
    }

    #[Test]
    public function returnsHttpUriIfUriHasDnsRecoed(): void
    {
        $this->checkdnsrr->returns(true);
        assertThat(
            $this->readParam('http://stubbles.net/')->asExistingHttpUri($this->checkdnsrr),
            equals(HttpUri::fromString('http://stubbles.net/'))
        );
    }

    #[Test]
    public function returnsNullIfUriHasNoDnsRecord(): void
    {
        assertNull(
            $this->readParam('http://doesnotexist')
                ->asExistingHttpUri($this->checkdnsrr)
        );
    }

    #[Test]
    public function addsErrorToParamIfUriHasNoDnsRecoed(): void
    {
        $this->readParam('http://doesnotexist')->asExistingHttpUri($this->checkdnsrr);
        assertTrue(
            $this->paramErrors->existForWithId('bar', 'HTTP_URI_NOT_AVAILABLE')
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asExistingHttpUriReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $httpUri = HttpUri::fromString('http://example.com/');
        assertThat(
            $this->readParam(null)
                ->defaultingTo($httpUri)
                ->asExistingHttpUri(),
            isSameAs($httpUri)
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asExistingHttpUriReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asExistingHttpUri());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asExistingHttpUriAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asExistingHttpUri();
        assertTrue($this->paramErrors->existForWithId('bar', 'HTTP_URI_MISSING'));
    }
}
