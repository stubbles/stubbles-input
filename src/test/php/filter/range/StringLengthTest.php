<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter\range;
use stubbles\lang\Secret;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\range\StringLength.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class StringLengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  StringLength
     */
    private $stringLength;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stringLength = new StringLength(1, 10);
    }

    /**
     * @return  array
     */
    public function outOfRangeValues()
    {
        return [
            [''],
            ['abcdefghijk'],
            [Secret::forNull()],
            [Secret::create('abcdefghijk')]
        ];
    }

    /**
     * @test
     * @dataProvider  outOfRangeValues
     */
    public function valueOutOfRangeIsNotContainedInRange($value)
    {
        assertFalse($this->stringLength->contains($value));
    }

    /**
     * @return  array
     */
    public function withinRangeValues()
    {
        return [
            ['a'],
            ['ab'],
            ['abcdefghi'],
            ['abcdefghij'],
            [Secret::create('a')],
            [Secret::create('ab')],
            [Secret::create('abcdefghi')],
            [Secret::create('abcdefghij')]
        ];
    }

    /**
     * @test
     * @dataProvider  withinRangeValues
     */
    public function valueWithinRangeIsContainedInRange($value)
    {
        assertTrue($this->stringLength->contains($value));
    }

    /**
     * @return  array
     */
    public function lowValues()
    {
        return [[''], [Secret::forNull()]];
    }

    /**
     * @test
     * @dataProvider  lowValues
     */
    public function rangeContainsLowValuesIfMinValueIsNull($value)
    {
        $numberRange = new StringLength(null, 10);
        assertTrue($numberRange->contains($value));
    }

    /**
     * @return  array
     */
    public function highValues()
    {
        return [[str_pad('a', 100)], [Secret::create(str_pad('a', 100))]];
    }

    /**
     * @test
     * @dataProvider  highValues
     */
    public function rangeContainsHighValuesIfMaxValueIsNull($value)
    {
        $numberRange = new StringLength(1, null);
        assertTrue($numberRange->contains($value));
    }

    /**
     * @return  array
     */
    public function ranges()
    {
        return [
            [new StringLength(1, 10)],
            [new StringLength(null, 10)],
            [new StringLength(1, null)]
        ];
    }

    /**
     * @test
     * @dataProvider  ranges
     */
    public function rangeDoesNotContainNull(StringLength $range)
    {
        assertFalse($range->contains(null));
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange()
    {
        assert($this->stringLength->errorsOf('foo'), equals([]));
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange()
    {
        assert(
                $this->stringLength->errorsOf(''),
                equals(['STRING_TOO_SHORT' => ['minLength' => 1]])
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange()
    {
        assert(
                $this->stringLength->errorsOf('abcdefghijk'),
                equals(['STRING_TOO_LONG' => ['maxLength' => 10]])
        );
    }

    /**
     * @return  array
     */
    public function truncateValues()
    {
        return [['foobar'], [Secret::create('foobar')]];
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     * @dataProvider  truncateValues
     */
    public function doesNotAllowTruncateByDefault($value)
    {
        assertFalse($this->stringLength->allowsTruncate($value));
    }

    /**
     * @test
     * @expectedException  LogicException
     * @since  2.3.1
     * @group  issue41
     * @dataProvider  truncateValues
     */
    public function truncateValueWhenNotAllowedThrowsLogicException($value)
    {
        $this->stringLength->truncateToMaxBorder($value);
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     * @dataProvider  truncateValues
     */
    public function allowsTruncateWhenCreatedThisWay($value)
    {
        assertTrue(StringLength::truncate(null, 3)->allowsTruncate($value));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  2.3.1
     * @group  issue41
     */
    public function createWithTruncateAndNoMaxLengthThrowsIllegalArgumentException()
    {
        StringLength::truncate(50, null);
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function truncateToMaxBorderReturnsSubstringWithMaxLength()
    {
        assert(
                StringLength::truncate(null, 3)->truncateToMaxBorder('foobar'),
                equals('foo')
        );
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function truncateToMaxBorderReturnsSecureSubstringWithMaxLength()
    {
        assert(
                StringLength::truncate(null, 3)
                            ->truncateToMaxBorder(Secret::create('foobar'))
                            ->unveil(),
                equals('foo')
        );
    }
}
