<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\filter\range\StringLength.
 *
 * @since  2.0.0
 * @group  filter
 * @group  filter_range
 */
class StringLengthTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  StringLength
     */
    private $stringLength;

    protected function setUp(): void
    {
        $this->stringLength = new StringLength(1, 10);
    }

    /**
     * @return  array<string[]>
     */
    public static function outOfRangeValues(): array
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
    public function valueOutOfRangeIsNotContainedInRange(string $value): void
    {
        assertFalse($this->stringLength->contains($value));
    }

    /**
     * @return  array<string[]>
     */
    public static function withinRangeValues(): array
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
    public function valueWithinRangeIsContainedInRange(string $value): void
    {
        assertTrue($this->stringLength->contains($value));
    }

    /**
     * @return  array<string[]>
     */
    public static function lowValues(): array
    {
        return [['']];
    }

    /**
     * @test
     * @dataProvider  lowValues
     */
    public function rangeContainsLowValuesIfMinValueIsNull(string $value): void
    {
        $numberRange = new StringLength(null, 10);
        assertTrue($numberRange->contains($value));
    }

    /**
     * @return  array<string[]>
     */
    public static function highValues(): array
    {
        return [[str_pad('a', 100)]];
    }

    /**
     * @test
     * @dataProvider  highValues
     */
    public function rangeContainsHighValuesIfMaxValueIsNull(string $value): void
    {
        $numberRange = new StringLength(1, null);
        assertTrue($numberRange->contains($value));
    }

    /**
     * @return  array<StringLength[]>
     */
    public static function ranges(): array
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
    public function rangeDoesNotContainNull(StringLength $range): void
    {
        assertFalse($range->contains(null));
    }

    /**
     * @test
     */
    public function errorListIsEmptyIfValueContainedInRange(): void
    {
        assertEmptyArray($this->stringLength->errorsOf('foo'));
    }

    /**
     * @test
     */
    public function errorListContainsMinBorderErrorWhenValueBelowRange(): void
    {
        assertThat(
                $this->stringLength->errorsOf(''),
                equals(['STRING_TOO_SHORT' => ['minLength' => 1]])
        );
    }

    /**
     * @test
     */
    public function errorListContainsMaxBorderErrorWhenValueAboveRange(): void
    {
        assertThat(
                $this->stringLength->errorsOf('abcdefghijk'),
                equals(['STRING_TOO_LONG' => ['maxLength' => 10]])
        );
    }

    /**
     * @return  array<string[]>
     */
    public static function truncateValues(): array
    {
        return [['foobar']];
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     * @dataProvider  truncateValues
     */
    public function doesNotAllowTruncateByDefault(string $value): void
    {
        assertFalse($this->stringLength->allowsTruncate($value));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     * @dataProvider  truncateValues
     */
    public function truncateValueWhenNotAllowedThrowsLogicException(string $value): void
    {
        expect(function() use ($value) {
                $this->stringLength->truncateToMaxBorder($value);
        })->throws(\LogicException::class);
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     * @dataProvider  truncateValues
     */
    public function allowsTruncateWhenCreatedThisWay(string $value): void
    {
        assertTrue(StringLength::truncate(null, 3)->allowsTruncate($value));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function createWithTruncateAndNoMaxLengthThrowsIllegalArgumentException(): void
    {
        expect(function() {
                StringLength::truncate(50, null);
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function truncateToMaxBorderReturnsSubstringWithMaxLength(): void
    {
        assertThat(
                StringLength::truncate(null, 3)->truncateToMaxBorder('foobar'),
                equals('foo')
        );
    }
}
