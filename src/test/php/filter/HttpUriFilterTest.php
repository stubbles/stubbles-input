<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\HttpUriFilter.
  */
#[Group('filter')]
class HttpUriFilterTest extends FilterTestBase
{
    #[Test]
    public function returnsUriWithoutPortIfItIsDefaultPort(): void
    {
        assertThat(
            $this->readParam('http://example.org')->asHttpUri(),
            equals(HttpUri::fromString('http://example.org/'))
        );
    }

    #[Test]
    public function returnsUriWithPortIfItIsNonDefaultPort(): void
    {
        assertThat(
            $this->readParam('http://example.org:45')->asHttpUri(),
            equals(HttpUri::fromString('http://example.org:45/'))
        );
    }

    #[Test]
    public function returnsNullForNull(): void
    {
        assertNull($this->readParam(null)->asHttpUri());
    }

    #[Test]
    public function returnsNullForEmptyValue(): void
    {
        assertNull($this->readParam('')->asHttpUri());
    }

    #[Test]
    public function returnsNullForInvalidUri(): void
    {
        assertNull($this->readParam('ftp://foobar.de/')->asHttpUri());
    }

    #[Test]
    public function addsErrorToParamForInvalidUri(): void
    {
        $this->readParam('ftp://foobar.de/')->asHttpUri();
        assertTrue(
            $this->paramErrors->existForWithId('bar', 'HTTP_URI_INCORRECT')
        );
    }

    #[Test]
    public function doesNotPerformDnsCheck(): void
    {
        assertThat(
            $this->readParam('http://doesnotexist')->asHttpUri(),
            equals(HttpUri::fromString('http://doesnotexist'))
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asHttpUriReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        assertThat(
            $this->readParam(null)
                ->defaultingTo(HttpUri::fromString('http://example.com/'))
                ->asHttpUri(),
            equals(HttpUri::fromString('http://example.com/'))
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asHttpUriReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asHttpUri());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asHttpUriAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asHttpUri();
        assertTrue($this->paramErrors->existForWithId('bar', 'HTTP_URI_MISSING'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asHttpUriReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asHttpUri());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asHttpUriAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asHttpUri();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asHttpUriReturnsValidValue(): void
    {
        assertThat(
            $this->readParam('http://example.com/')->asHttpUri(),
            equals(HttpUri::fromString('http://example.com/'))
        );

    }
}
