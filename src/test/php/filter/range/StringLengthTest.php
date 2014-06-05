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
            ['abcdefghijk']
        ];
    }

    /**
     * @test
     * @dataProvider  outOfRangeValues
     */
    public function valueOutOfRangeIsNotContainedInRange($value)
    {
        $this->assertFalse(
                $this->stringLength->contains($value)
        );
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
            ['abcdefghij']
        ];
    }

    /**
     * @test
     * @dataProvider  withinRangeValues
     */
    public function valueWithinRangeIsContainedInRange($value)
    {
        $this->assertTrue(
                $this->stringLength->contains($value)
        );
    }

    /**
     * @test
     */
    public function rangeContainsLowValuesIfMinValueIsNull()
    {
        $numberRange = new StringLength(null, 10);
        $this->assertTrue($numberRange->contains(''));
    }

    /**
     * @test
     */
    public function rangeContainsHighValuesIfMaxValueIsNull()
    {
        $numberRange = new StringLength(1, null);
        $this->assertTrue($numberRange->contains(str_pad('a', 100)));
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
        $this->assertFalse($range->contains(null));
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange()
    {
        $this->assertEquals(
                [],
                $this->stringLength->errorsOf('foo')
        );
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange()
    {
        $this->assertEquals(
                ['STRING_TOO_SHORT' => ['minLength' => 1]],
                $this->stringLength->errorsOf('')
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange()
    {
        $this->assertEquals(
                ['STRING_TOO_LONG' => ['maxLength' => 10]],
                $this->stringLength->errorsOf('abcdefghijk')
        );
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function doesNotAllowTruncateByDefault()
    {
        $this->assertFalse($this->stringLength->allowsTruncate('foobar'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
     * @since  2.3.1
     * @group  issue41
     */
    public function truncateValueWhenNotAllowedThrowsRuntimeException()
    {
        $this->stringLength->truncateToMaxBorder('foobar');
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function allowsTruncateWhenCreatedThisWay()
    {
        $this->assertTrue(StringLength::truncate(null, 3)->allowsTruncate('foobar'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        $this->assertEquals(
                'foo',
                StringLength::truncate(null, 3)->truncateToMaxBorder('foobar')
        );
    }
}
