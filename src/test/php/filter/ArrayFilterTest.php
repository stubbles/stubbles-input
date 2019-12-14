<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use function bovigo\assert\{
    assertThat,
    assertEmptyArray,
    assertNull,
    assertTrue,
    predicate\equals
};
/**
 * Tests for stubbles\input\ValueReader::asArray().
 *
 * @since  2.0.0
 * @group  filter
 */
class ArrayFilterTest extends FilterTest
{
    /**
     * @return  array<mixed[]>
     */
    public function valueResultTuples(): array
    {
        return [[null, null],
                ['', []],
                ['foo', ['foo']],
                [' foo ', ['foo']],
                ['foo, bar', ['foo', 'bar']],
        ];
    }

    /**
     * @param  mixed  $value
     * @param  mixed  $expected
     * @test
     * @dataProvider  valueResultTuples
     */
    public function value($value, $expected): void
    {
        assertThat($this->readParam($value)->asArray(), equals($expected));
    }

    /**
     * @test
     */
    public function usingDifferentSeparator(): void
    {
        assertThat($this->readParam('foo|bar')->asArray('|'), equals(['foo', 'bar']));
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function asArrayReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asArray());
    }

    /**
     * @test
     */
    public function asArrayAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asArray();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }
}
