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
use bovigo\callmap\NewInstance;
use stubbles\values\Secret;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
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

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->passwordChecker = NewInstance::of(PasswordChecker::class);
        $this->passwordFilter  = new PasswordFilter($this->passwordChecker);
        parent::setUp();
    }

    /**
     * @param  string  $expectedPassword
     * @param  Secret  $actualPassword
     */
    private function assertPasswordEquals($expectedPassword, Secret $actualPassword)
    {
        assert($actualPassword->unveil(), equals($expectedPassword));
    }

    /**
     * @test
     */
    public function returnsValueWhenCheckerDoesNotObject()
    {
        $this->passwordChecker->mapCalls(['check' => []]);
        $this->assertPasswordEquals(
                '425%$%"�$%t 32',
                $this->passwordFilter->apply($this->createParam('425%$%"�$%t 32'))
        );
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        assertNull($this->passwordFilter->apply($this->createParam(null)));
        verify($this->passwordChecker, 'check')->wasNeverCalled();

    }

    /**
     * @test
     */
    public function returnsNullForEmptyString()
    {
        assertNull($this->passwordFilter->apply($this->createParam('')));
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsPasswordIfBothArrayValuesAreEqual()
    {
        $this->passwordChecker->mapCalls(['check' => []]);
        $this->assertPasswordEquals(
                'foo',
                $this->passwordFilter->apply($this->createParam(['foo', 'foo']))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBothArrayValuesAreDifferent()
    {
        assertNull(
                $this->passwordFilter->apply($this->createParam(['foo', 'bar']))
        );
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenBothArrayValuesAreDifferent()
    {
        $param = $this->createParam(['foo', 'bar']);
        $this->passwordFilter->apply($param);
        assertTrue($param->hasError('PASSWORDS_NOT_EQUAL'));
        verify($this->passwordChecker, 'check')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsNullIfCheckerReportsErrors()
    {
        $this->passwordChecker->mapCalls(
                ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        assertNull($this->passwordFilter->apply($this->createParam('bar')));
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenValueIsNotAllowed()
    {
        $this->passwordChecker->mapCalls(
                ['check' => ['PASSWORD_TOO_SHORT' => ['minLength' => 8]]]
        );
        $param = $this->createParam('bar');
        $this->passwordFilter->apply($param);
        assertTrue($param->hasError('PASSWORD_TOO_SHORT'));
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
     * @expectedException  BadMethodCallException
     */
    public function asPasswordWithDefaultValueThrowsBadMethodCallException()
    {
        $this->readParam(null)
                ->defaultingTo('secret')
                ->asPassword($this->passwordChecker);
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
        $this->passwordChecker->mapCalls(
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
        $this->passwordChecker->mapCalls(
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
        $this->passwordChecker->mapCalls(['check' => []]);
        $this->assertPasswordEquals(
                'abcde',
                $this->readParam('abcde')->asPassword($this->passwordChecker)
        );
    }
}
