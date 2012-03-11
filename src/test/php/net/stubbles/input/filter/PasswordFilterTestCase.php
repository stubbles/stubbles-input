<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\PasswordFilter.
 *
 * @group  filter
 */
class PasswordFilterTestCase extends FilterTestCase
{
    /**
     * the instance to test
     *
     * @type  PasswordFilter
     */
    protected $passwordFilter;

    /**
     * create test environment
     *
     */
    public function setUp()
    {
        $this->passwordFilter = new PasswordFilter();
        $this->passwordFilter->minDiffChars(null);
    }

    /**
     * @test
     */
    public function value()
    {
        $this->assertEquals('foo', $this->passwordFilter->apply($this->createParam('foo')));
        $this->assertEquals('425%$%"�$%t 32', $this->passwordFilter->apply($this->createParam('425%$%"�$%t 32')));
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
        $this->assertEquals('foo', $this->passwordFilter->apply($this->createParam(array('foo', 'foo'))));
    }

    /**
     * @test
     */
    public function returnsNullIfBothArrayValuesAreDifferent()
    {
        $this->assertNull($this->passwordFilter->apply($this->createParam(array('foo', 'bar'))));
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenBothArrayValuesAreDifferent()
    {
        $param = $this->createParam(array('foo', 'bar'));
        $this->passwordFilter->apply($param);
        $this->assertTrue($param->hasError('PASSWORDS_NOT_EQUAL'));
    }

    /**
     * @test
     */
    public function returnsNullIfValueIsNotAllowed()
    {
        $this->assertNull($this->passwordFilter->disallowValues(array('bar'))
                                               ->apply($this->createParam('bar'))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenValueIsNotAllowed()
    {
        $param = $this->createParam('bar');
        $this->passwordFilter->disallowValues(array('bar'))
                             ->apply($param);
        $this->assertTrue($param->hasError('PASSWORD_INVALID'));
    }

    /**
     * @test
     */
    public function returnsPasswordIfValueHasGivenAmountOfDifferentCharacters()
    {
        $this->assertEquals('abcde',
                            $this->passwordFilter->minDiffChars(5)
                                                 ->apply($this->createParam(array('abcde', 'abcde')))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfValueHasLessThenGivenAmountOfDifferentCharacters()
    {
        $this->assertNull($this->passwordFilter->minDiffChars(5)
                                               ->apply($this->createParam(array('abcdd', 'abcdd')))
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamWhenValueHasLessThenGivenAmountOfDifferentCharacters()
    {
        $param = $this->createParam(array('abcdd', 'abcdd'));
        $this->passwordFilter->minDiffChars(5)
                             ->apply($param);
        $this->assertTrue($param->hasError('PASSWORD_TOO_LESS_DIFF_CHARS'));
    }
}
?>