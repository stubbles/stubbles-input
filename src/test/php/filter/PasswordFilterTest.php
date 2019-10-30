<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use bovigo\callmap\NewInstance;
use stubbles\values\Secret;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;

require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\PasswordFilter.
 *
 * @group  filter
 */
class PasswordFilterTest extends FilterTest
{
    /**
     * the instance to test
     *
     * @type  PasswordFilter
     */
    private $passwordFilter;
    /**
     * mocked password checker
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $passwordChecker;

    protected function setUp(): void
    {
        $this->passwordChecker = NewInstance::of(PasswordChecker::class);
        $this->passwordFilter  = new PasswordFilter($this->passwordChecker);
        parent::setUp();
    }

    /**
     * @param  string  $expectedPassword
     * @param  Secret  $actualPassword
     */
    private function assertPasswordEquals(string $expectedPassword, Secret $actualPassword)
    {
        assertThat($actualPassword->unveil(), equals($expectedPassword));
    }

    /**
     * @test
     */
    public function returnsValueWhenCheckerDoesNotObject()
    {
        $this->passwordChecker->returns(['check' => []]);
        $this->assertPasswordEquals(
                '425%$%"�$%t 32',
                $this->passwordFilter->apply($this->createParam('425%$%"�$%t 32'))[0]
        );
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        assertNull($this->passwordFilter->apply($this->createParam(null))[0]);
        verify($this->passwordChecker, 'check')->wasNeverCalled();

    }

    /**
     * @test
     */
    public function returnsNullForEmptyString()
    {
        assertNull($this->passwordFilter->apply($this->createParam(''))[0]);
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsPasswordIfBothArrayValuesAreEqual()
    {
        $this->passwordChecker->returns(['check' => []]);
        $this->assertPasswordEquals(
                'foo',
                $this->passwordFilter->apply($this->createParam(['foo', 'foo']))[0]
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBothArrayValuesAreDifferent()
    {
        assertNull(
                $this->passwordFilter->apply($this->createParam(['foo', 'bar']))[0]
        );
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenBothArrayValuesAreDifferent()
    {
        $param = $this->createParam(['foo', 'bar']);
        list($_, $errors) = $this->passwordFilter->apply($param);
        assertTrue(isset($errors['PASSWORDS_NOT_EQUAL']));
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsNullIfCheckerReportsErrors()
    {
        $this->passwordChecker->returns(
                ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        assertNull($this->passwordFilter->apply($this->createParam('bar'))[0]);
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenValueIsNotAllowed()
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
     * @test
     */
    public function asPasswordReturnsNullIfParamIsNullAndRequired()
    {
        assertNull(
                $this->readParam(null)
                        ->required()
                        ->asPassword($this->passwordChecker)
        );
    }

    /**
     * @test
     */
    public function asPasswordWithDefaultValueThrowsBadMethodCallException()
    {
        expect(function() {
                $this->readParam(null)
                        ->defaultingTo('secret')
                        ->asPassword($this->passwordChecker);
        })->throws(\BadMethodCallException::class);
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asPassword($this->passwordChecker);
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordReturnsNullIfParamIsInvalid()
    {
        $this->passwordChecker->returns(
                ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        assertNull($this->readParam('foo')->asPassword($this->passwordChecker));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsInvalid()
    {
        $this->passwordChecker->returns(
                ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        $this->readParam('foo')->asPassword($this->passwordChecker);
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asPasswordReturnsValidValue()
    {
        $this->passwordChecker->returns(['check' => []]);
        $this->assertPasswordEquals(
                'abcde',
                $this->readParam('abcde')->asPassword($this->passwordChecker)
        );
    }
}
