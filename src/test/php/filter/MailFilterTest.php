<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
     * @type  Filter
     */
    private $mailFilter;

    protected function setUp(): void
    {
        $this->mailFilter = MailFilter::instance();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsNullWhenValueIsNull()
    {
        assertNull($this->mailFilter->apply($this->createParam(null))[0]);
    }

    /**
     * @test
     */
    public function returnsNullWhenValueIsEmpty()
    {
        assertNull($this->mailFilter->apply($this->createParam(''))[0]);
    }

    /**
     * @test
     */
    public function returnsFilteredValue()
    {
        assertThat(
                $this->mailFilter->apply($this->createParam('example@example.org'))[0],
                equals('example@example.org')
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
                $this->mailFilter->apply($this->createParam('space in@mailadre.ss'))[0]
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenSpaceInValue()
    {
        $param = $this->createParam('space in@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_CANNOT_CONTAIN_SPACES']));
    }

    /**
     * @test
     */
    public function returnsNullWhenGermanUmlautInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('föö@mailadre.ss'))[0]
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenGermanUmlautInValue()
    {
        $param = $this->createParam('föö@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_CANNOT_CONTAIN_UMLAUTS']));
    }

    /**
     * @test
     */
    public function returnsNullWhenMoreThanOneAtCharacterInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('foo@bar@mailadre.ss'))[0]
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenMoreThanOneAtCharacterInValue()
    {
        $param = $this->createParam('foo@bar@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_MUST_CONTAIN_ONE_AT']));
    }

    /**
     * @test
     */
    public function returnsNullWhenDotBeforeAtSign()
    {
        assertNull($this->mailFilter->apply(
                $this->createParam('foo.@mailadre.ss')
        )[0]);
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenDotBeforeAtSign()
    {
        $param = $this->createParam('foo.@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_DOT_NEXT_TO_AT_SIGN']));
    }

    /**
     * @test
     */
    public function returnsNullWhenDotAfterAtSign()
    {
        assertNull($this->mailFilter->apply(
                $this->createParam('foo@.mailadre.ss')
        )[0]);
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenDotAfterAtSign()
    {
        $param = $this->createParam('foo@.mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_DOT_NEXT_TO_AT_SIGN']));
    }

    /**
     * @test
     */
    public function returnsNullWhenTwoFollowingDotsInValue()
    {
        assertNull(
                $this->mailFilter->apply($this->createParam('foo..bar@mailadre.ss'))[0]
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenTwoFollowingDotsInValue()
    {
        $param = $this->createParam('foo..bar@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS']));
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        assertEmptyString($this->readParam(null)->asMailAddress());
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function asMailAddressReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assertThat(
                $this->readParam(null)
                        ->defaultingTo('baz@example.org')
                        ->asMailAddress(),
                equals('baz@example.org')
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
        assertThat(
                $this->readParam('foo@example.org')->asMailAddress(),
                equals('foo@example.org')
        );
    }
}
