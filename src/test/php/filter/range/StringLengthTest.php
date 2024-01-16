<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;

use Generator;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
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
 */
#[Group('filter')]
#[Group('filter_range')]
class StringLengthTest extends TestCase
{
    private StringLength $stringLength;

    protected function setUp(): void
    {
        $this->stringLength = new StringLength(1, 10);
    }

    public static function outOfRangeValues(): Generator
    {
        yield [''];
        yield ['abcdefghijk'];
    }

    #[Test]
    #[DataProvider('outOfRangeValues')]
    public function valueOutOfRangeIsNotContainedInRange(string $value): void
    {
        assertFalse($this->stringLength->contains($value));
    }

    public static function withinRangeValues(): Generator
    {
        yield ['a'];
        yield ['ab'];
        yield ['abcdefghi'];
        yield ['abcdefghij'];
    }

    #[Test]
    #[DataProvider('withinRangeValues')]
    public function valueWithinRangeIsContainedInRange(string $value): void
    {
        assertTrue($this->stringLength->contains($value));
    }

    public static function lowValues(): Generator
    {
        yield [''];
    }

    #[Test]
    #[DataProvider('lowValues')]
    public function rangeContainsLowValuesIfMinValueIsNull(string $value): void
    {
        $numberRange = new StringLength(null, 10);
        assertTrue($numberRange->contains($value));
    }

    public static function highValues(): Generator
    {
        yield [str_pad('a', 100)];
    }

    #[Test]
    #[DataProvider('highValues')]
    public function rangeContainsHighValuesIfMaxValueIsNull(string $value): void
    {
        $numberRange = new StringLength(1, null);
        assertTrue($numberRange->contains($value));
    }

    public static function ranges(): Generator
    {
        yield [new StringLength(1, 10)];
        yield [new StringLength(null, 10)];
        yield [new StringLength(1, null)];
    }

    #[Test]
    #[DataProvider('ranges')]
    public function rangeDoesNotContainNull(StringLength $range): void
    {
        assertFalse($range->contains(null));
    }

    #[Test]
    public function errorListIsEmptyIfValueContainedInRange(): void
    {
        assertEmptyArray($this->stringLength->errorsOf('foo'));
    }

    #[Test]
    public function errorListContainsMinBorderErrorWhenValueBelowRange(): void
    {
        assertThat(
            $this->stringLength->errorsOf(''),
            equals(['STRING_TOO_SHORT' => ['minLength' => 1]])
        );
    }

    #[Test]
    public function errorListContainsMaxBorderErrorWhenValueAboveRange(): void
    {
        assertThat(
            $this->stringLength->errorsOf('abcdefghijk'),
            equals(['STRING_TOO_LONG' => ['maxLength' => 10]])
        );
    }

    public static function truncateValues(): Generator
    {
        yield ['foobar'];
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    #[DataProvider('truncateValues')]
    public function doesNotAllowTruncateByDefault(string $value): void
    {
        assertFalse($this->stringLength->allowsTruncate($value));
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    #[DataProvider('truncateValues')]
    public function truncateValueWhenNotAllowedThrowsLogicException(string $value): void
    {
        expect(function() use ($value) {
            $this->stringLength->truncateToMaxBorder($value);
        })->throws(LogicException::class);
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    #[DataProvider('truncateValues')]
    public function allowsTruncateWhenCreatedThisWay(string $value): void
    {
        assertTrue(StringLength::truncate(null, 3)->allowsTruncate($value));
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[TestWith([0])]
    #[TestWith([-1])]
    #[Group('issue41')]
    public function createWithTruncateAndZeroMaxLengthThrowsIllegalArgumentException(
        int $maxLength
    ): void {
        expect(fn() => StringLength::truncate(50, $maxLength))
            ->throws(InvalidArgumentException::class);
    }

    /**
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    public function truncateToMaxBorderReturnsSubstringWithMaxLength(): void
    {
        assertThat(
            StringLength::truncate(null, 3)->truncateToMaxBorder('foobar'),
            equals('foo')
        );
    }
}
