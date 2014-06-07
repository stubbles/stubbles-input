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
use stubbles\lang\SecureString;
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
     * create test environment
     *
     */
    public function setUp()
    {
        $this->passwordFilter = new PasswordFilter();
        $this->passwordFilter->minDiffChars(null);
        parent::setUp();
    }

    /**
     * @param  string        $expectedPassword
     * @param  SecureString  $actualPassword
     */
    private function assertPasswordEquals($expectedPassword, SecureString $actualPassword)
    {
        $this->assertEquals(
                $expectedPassword,
                $actualPassword->unveil()
        );
    }

    /**
     * @test
     */
    public function value()
    {
        $this->assertPasswordEquals('foo', $this->passwordFilter->apply($this->createParam('foo')));
        $this->assertPasswordEquals('425%$%"�$%t 32', $this->passwordFilter->apply($this->createParam('425%$%"�$%t 32')));
    }

    /**
     * @test
     */
    public function returnsNullForNull()
    {
        $this->assertNull($this->passwordFilter->apply($this->createParam(null)));

    }

    /**
     * @test
     */
    public function returnsNullForEmptyString()
    {
        $this->assertNull($this->passwordFilter->apply($this->createParam('')));
    }

    /**
     * @test
     */
    public function returnsPasswordIfBothArrayValuesAreEqual()
    {
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
        $this->assertNull($this->passwordFilter->apply($this->createParam(['foo', 'bar'])));
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenBothArrayValuesAreDifferent()
    {
        $param = $this->createParam(['foo', 'bar']);
        $this->passwordFilter->apply($param);
        $this->assertTrue($param->hasError('PASSWORDS_NOT_EQUAL'));
    }

    /**
     * @test
     */
    public function returnsNullIfValueIsNotAllowed()
    {
        $this->assertNull($this->passwordFilter->disallowValues(['bar'])
                                               ->apply($this->createParam('bar'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenValueIsNotAllowed()
    {
        $param = $this->createParam('bar');
        $this->passwordFilter->disallowValues(['bar'])
                             ->apply($param);
        $this->assertTrue($param->hasError('PASSWORD_INVALID'));
    }

    /**
     * @test
     */
    public function returnsPasswordIfValueHasGivenAmountOfDifferentCharacters()
    {
        $this->assertPasswordEquals(
                'abcde',
                $this->passwordFilter->minDiffChars(5)
                                     ->apply($this->createParam(['abcde', 'abcde']))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfValueHasLessThenGivenAmountOfDifferentCharacters()
    {
        $this->assertNull($this->passwordFilter->minDiffChars(5)
                                               ->apply($this->createParam(['abcdd', 'abcdd']))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenValueHasLessThenGivenAmountOfDifferentCharacters()
    {
        $param = $this->createParam(['abcdd', 'abcdd']);
        $this->passwordFilter->minDiffChars(5)
                             ->apply($param);
        $this->assertTrue($param->hasError('PASSWORD_TOO_LESS_DIFF_CHARS'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asPassword());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asPassword();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')->asPassword());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asPassword();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asPasswordReturnsValidValue()
    {
        $this->assertPasswordEquals(
                'abcde',
                $this->createValueReader('abcde')->asPassword()
        );
    }
}
