<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function bovigo\assert\{
    assertThat,
    assertNull,
    assertTrue,
    predicate\equals
};
/**
 * Tests for stubbles\input\ValueReader::asArray().
 *
 * @since  2.0.0
 */
#[Group('filter')]
class ArrayFilterTest extends FilterTestBase
{
    public static function valueResultTuples(): Generator
    {
        yield [null, null];
        yield ['', []];
        yield ['foo', ['foo']];
        yield [' foo ', ['foo']];
        yield ['foo, bar', ['foo', 'bar']];
    }

    #[Test]
    #[DataProvider('valueResultTuples')]
    public function value(?string $value, ?array $expected): void
    {
        assertThat($this->readParam($value)->asArray(), equals($expected));
    }

    #[Test]
    public function usingDifferentSeparator(): void
    {
        assertThat($this->readParam('foo|bar')->asArray('|'), equals(['foo', 'bar']));
    }

    #[Test]
    public function asArrayReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $default = ['foo' => 'bar'];
        assertThat(
            $this->readParam(null)
                ->defaultingTo($default)
                ->asArray(),
            equals($default)
        );
    }

    #[Test]
    public function asArrayReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asArray());
    }

    #[Test]
    public function asArrayAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asArray();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }
}
