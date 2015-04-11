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
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\MailFilter.
 *
 * @group  filter
 */
class MailFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @var  MailFilter
     */
    private $mailFilter;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->mailFilter = MailFilter::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsNullWhenValueIsNull()
    {
        assertNull($this->mailFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function returnsNullWhenValueIsEmpty()
    {
        assertNull($this->mailFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function returnsFilteredValue()
    {
        assertEquals(
                'example@example.org',
                $this->mailFilter->apply($this->createParam('example@example.org'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asMailAddress());
    }

    /**
     * @test
     */
    public function addsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asMailAddress();
        assertTrue(
                $this->paramErrors->existForWithId('bar', 'MAILADDRESS_MISSING')
        );
    }

    /**
     * @test
     */
    public function returnsNullWhenSpaceInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('space in@mailadre.ss'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenSpaceInValue()
    {
        $param = $this->createParam('space in@mailadre.ss');
        $this->mailFilter->apply($param);
        assertTrue($param->hasError('MAILADDRESS_CANNOT_CONTAIN_SPACES'));
    }

    /**
     * @test
     */
    public function returnsNullWhenGermanUmlautInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('föö@mailadre.ss'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenGermanUmlautInValue()
    {
        $param = $this->createParam('föö@mailadre.ss');
        $this->mailFilter->apply($param);
        assertTrue($param->hasError('MAILADDRESS_CANNOT_CONTAIN_UMLAUTS'));
    }

    /**
     * @test
     */
    public function returnsNullWhenMoreThanOneAtCharacterInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('foo@bar@mailadre.ss'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenMoreThanOneAtCharacterInValue()
    {
        $param = $this->createParam('foo@bar@mailadre.ss');
        $this->mailFilter->apply($param);
        assertTrue($param->hasError('MAILADDRESS_MUST_CONTAIN_ONE_AT'));
    }

    /**
     * @test
     */
    public function returnsNullWhenIllegalCharsInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('foo&/4@mailadre.ss'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenIllegalCharsInValue()
    {
        $param = $this->createParam('foo&/4@mailadre.ss');
        $this->mailFilter->apply($param);
        assertTrue($param->hasError('MAILADDRESS_CONTAINS_ILLEGAL_CHARS'));
    }

    /**
     * @test
     */
    public function returnsNullWhenTwoFollowingDotsInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('foo..bar@mailadre.ss'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenTwoFollowingDotsInValue()
    {
        $param = $this->createParam('foo..bar@mailadre.ss');
        $this->mailFilter->apply($param);
        assertTrue($param->hasError('MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS'));
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        assertEquals('', $this->readParam(null)->asMailAddress());
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assertEquals(
                'baz@example.org',
                $this->readParam(null)
                        ->defaultingTo('baz@example.org')
                        ->asMailAddress()
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asMailAddress());
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asMailAddress();
        assertTrue(
                $this->paramErrors->existForWithId('bar', 'MAILADDRESS_MISSING')
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asMailAddress());
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asMailAddress();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressReturnsValidValue()
    {
        assertEquals(
                'foo@example.org',
                $this->readParam('foo@example.org')->asMailAddress()
        );
    }
}
