<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use BadMethodCallException;
use bovigo\callmap\ClassProxy;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Test;
use stubbles\values\Secret;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Tests for stubbles\input\filter\PasswordFilter.
 *
 * @group  filter
 */
class PasswordFilterTest extends FilterTestBase
{
    private PasswordFilter $passwordFilter;
    private PasswordChecker&ClassProxy $passwordChecker;

    protected function setUp(): void
    {
        $this->passwordChecker = NewInstance::of(PasswordChecker::class);
        $this->passwordFilter  = new PasswordFilter($this->passwordChecker);
        parent::setUp();
    }

    private function assertPasswordEquals(
        string $expectedPassword,
        Secret $actualPassword = null
    ): void {
        assertThat($actualPassword?->unveil(), equals($expectedPassword));
    }

    #[Test]
    public function returnsValueWhenCheckerDoesNotObject(): void
    {
        $this->passwordChecker->returns(['check' => []]);
        $this->assertPasswordEquals(
            '425%$%"�$%t 32',
            $this->passwordFilter->apply($this->createParam('425%$%"�$%t 32'))[0]
        );
    }

    #[Test]
    public function returnsNullForNull(): void
    {
        assertNull($this->passwordFilter->apply($this->createParam(null))[0]);
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    #[Test]
    public function returnsNullForEmptyString(): void
    {
        assertNull($this->passwordFilter->apply($this->createParam(''))[0]);
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    #[Test]
    public function returnsPasswordIfBothArrayValuesAreEqual(): void
    {
        $this->passwordChecker->returns(['check' => []]);
        $this->assertPasswordEquals(
            'foo',
            $this->passwordFilter->apply($this->createParam(['foo', 'foo']))[0]
        );
    }

    #[Test]
    public function returnsNullIfBothArrayValuesAreDifferent(): void
    {
        assertNull(
            $this->passwordFilter->apply($this->createParam(['foo', 'bar']))[0]
        );
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    #[Test]
    public function addsErrorToParamWhenBothArrayValuesAreDifferent(): void
    {
        $param = $this->createParam(['foo', 'bar']);
        list($_, $errors) = $this->passwordFilter->apply($param);
        assertTrue(isset($errors['PASSWORDS_NOT_EQUAL']));
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    #[Test]
    public function returnsNullIfCheckerReportsErrors(): void
    {
        $this->passwordChecker->returns(
            ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        assertNull($this->passwordFilter->apply($this->createParam('bar'))[0]);
    }

    #[Test]
    public function addsErrorToParamWhenValueIsNotAllowed(): void
    {
        $this->passwordChecker->returns(
            ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        $param = $this->createParam('bar');
        list($_, $errors) = $this->passwordFilter->apply($param);
        assertTrue(isset($errors['PASSWORD_TOO_SHORT']));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asPasswordReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull(
            $this->readParam(null)
                ->required()
                ->asPassword($this->passwordChecker)
        );
    }

    #[Test]
    public function asPasswordWithDefaultValueThrowsBadMethodCallException(): void
    {
        expect(function() {
            $this->readParam(null)
                ->defaultingTo('secret')
                ->asPassword($this->passwordChecker);
        })->throws(BadMethodCallException::class);
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asPasswordAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asPassword($this->passwordChecker);
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asPasswordReturnsNullIfParamIsInvalid(): void
    {
        $this->passwordChecker->returns(
            ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        assertNull($this->readParam('foo')->asPassword($this->passwordChecker));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asPasswordAddsParamErrorIfParamIsInvalid(): void
    {
        $this->passwordChecker->returns(
            ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        $this->readParam('foo')->asPassword($this->passwordChecker);
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asPasswordReturnsValidValue(): void
    {
        $this->passwordChecker->returns(['check' => []]);
        $this->assertPasswordEquals(
            'abcde',
            $this->readParam('abcde')->asPassword($this->passwordChecker)
        );
    }
}
