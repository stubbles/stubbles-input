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

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\MailFilter.
 */
#[Group('filter')]
class MailFilterTest extends FilterTestBase
{
    private MailFilter $mailFilter;

    protected function setUp(): void
    {
        $this->mailFilter = MailFilter::instance();
        parent::setUp();
    }

    #[Test]
    public function returnsNullWhenValueIsNull(): void
    {
        assertNull($this->mailFilter->apply($this->createParam(null))[0]);
    }

    #[Test]
    public function returnsNullWhenValueIsEmpty(): void
    {
        assertNull($this->mailFilter->apply($this->createParam(''))[0]);
    }

    #[Test]
    public function returnsFilteredValue(): void
    {
        assertThat(
            $this->mailFilter->apply($this->createParam('example@example.org'))[0],
            equals('example@example.org')
        );
    }

    #[Test]
    public function returnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asMailAddress());
    }

    #[Test]
    public function addsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asMailAddress();
        assertTrue(
            $this->paramErrors->existForWithId('bar', 'MAILADDRESS_MISSING')
        );
    }

    #[Test]
    public function returnsNullWhenSpaceInValue(): void
    {
        assertNull(
            $this->mailFilter->apply($this->createParam('space in@mailadre.ss'))[0]
        );
    }

    #[Test]
    public function addsErrorToParamWhenSpaceInValue(): void
    {
        $param = $this->createParam('space in@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_CANNOT_CONTAIN_SPACES']));
    }

    #[Test]
    public function returnsNullWhenGermanUmlautInValue(): void
    {
        assertNull(
            $this->mailFilter->apply($this->createParam('föö@mailadre.ss'))[0]
        );
    }

    #[Test]
    public function addsErrorToParamWhenGermanUmlautInValue(): void
    {
        $param = $this->createParam('föö@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_CANNOT_CONTAIN_UMLAUTS']));
    }

    #[Test]
    public function returnsNullWhenMoreThanOneAtCharacterInValue(): void
    {
        assertNull(
            $this->mailFilter->apply($this->createParam('foo@bar@mailadre.ss'))[0]
        );
    }

    #[Test]
    public function addsErrorToParamWhenMoreThanOneAtCharacterInValue(): void
    {
        $param = $this->createParam('foo@bar@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_MUST_CONTAIN_ONE_AT']));
    }

    #[Test]
    public function returnsNullWhenDotBeforeAtSign(): void
    {
        assertNull($this->mailFilter->apply(
                $this->createParam('foo.@mailadre.ss')
        )[0]);
    }

    #[Test]
    public function addsErrorToParamWhenDotBeforeAtSign(): void
    {
        $param = $this->createParam('foo.@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_DOT_NEXT_TO_AT_SIGN']));
    }

    #[Test]
    public function returnsNullWhenDotAfterAtSign(): void
    {
        assertNull($this->mailFilter->apply(
                $this->createParam('foo@.mailadre.ss')
        )[0]);
    }

    #[Test]
    public function addsErrorToParamWhenDotAfterAtSign(): void
    {
        $param = $this->createParam('foo@.mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_DOT_NEXT_TO_AT_SIGN']));
    }

    #[Test]
    public function returnsNullWhenTwoFollowingDotsInValue(): void
    {
        assertNull(
            $this->mailFilter->apply($this->createParam('foo..bar@mailadre.ss'))[0]
        );
    }

    #[Test]
    public function addsErrorToParamWhenTwoFollowingDotsInValue(): void
    {
        $param = $this->createParam('foo..bar@mailadre.ss');
        list($_, $errors) = $this->mailFilter->apply($param);
        assertTrue(isset($errors['MAILADDRESS_CONTAINS_TWO_FOLLOWING_DOTS']));
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function asMailAddressReturnsEmptyStringIfParamIsNullAndNotRequired(): void
    {
        assertEmptyString($this->readParam(null)->asMailAddress());
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function asMailAddressReturnsDefaultIfParamIsNullAndNotRequired(): void
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
     */
    #[Test]
    public function asMailAddressReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asMailAddress());
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function asMailAddressAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asMailAddress();
        assertTrue(
            $this->paramErrors->existForWithId('bar', 'MAILADDRESS_MISSING')
        );
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function asStringReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asMailAddress());
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function asMailAddressAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asMailAddress();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function asMailAddressReturnsValidValue(): void
    {
        assertThat(
            $this->readParam('foo@example.org')->asMailAddress(),
            equals('foo@example.org')
        );
    }
}
