<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\HttpUriFilter.
 *
 * @group  filter
 */
class HttpUriFilterTest extends FilterTest
{
    /**
     * @test
     */
    public function returnsUriWithoutPortIfItIsDefaultPort()
    {
        assertThat(
                $this->readParam('http://example.org')->asHttpUri(),
                equals(HttpUri::fromString('http://example.org/'))
        );
    }



    /**
     * @test
     */
    public function returnsUriWithPortIfItIsNonDefaultPort()
    {
        assertThat(
                $this->readParam('http://example.org:45')->asHttpUri(),
                equals(HttpUri::fromString('http://example.org:45/'))
        );
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        assertNull($this->readParam(null)->asHttpUri());
    }

    /**
     * @test
     */
    public function returnsNullForEmptyValue()
    {
        assertNull($this->readParam('')->asHttpUri());
    }

    /**
     * @test
     */
    public function returnsNullForInvalidUri()
    {
        assertNull($this->readParam('ftp://foobar.de/')->asHttpUri());
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidUri()
    {
        $this->readParam('ftp://foobar.de/')->asHttpUri();
        assertTrue(
                $this->paramErrors->existForWithId('bar', 'HTTP_URI_INCORRECT')
        );
    }

    /**
     * @test
     */
    public function doesNotPerformDnsCheck()
    {
        assertThat(
                $this->readParam('http://doesnotexist')->asHttpUri(),
                equals(HttpUri::fromString('http://doesnotexist'))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsDefaultIfParamIsNullAndNotRequired()
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
        assertThat(
                $this->readParam('http://example.com/')->asHttpUri(),
                equals(HttpUri::fromString('http://example.com/'))
        );

    }
}
