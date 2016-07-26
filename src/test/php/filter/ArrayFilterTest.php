<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\ArrayFilter.
 *
 * @since  2.0.0
 * @group  filter
 */
class ArrayFilterTest extends FilterTest
{
    public function getValueResultTuples(): array
    {
        return [[null, null],
                ['', []],
                ['foo', ['foo']],
                [' foo ', ['foo']],
                ['foo, bar', ['foo', 'bar']],
        ];
    }

    /**
     * @param  scalar  $value
     * @param  array   $expected
     * @test
     * @dataProvider  getValueResultTuples
     */
    public function value($value, $expected)
    {
        $arrayFilter = new ArrayFilter();
        assert(
                $arrayFilter->apply($this->createParam($value)),
                equals($expected)
        );
    }

    /**
     * @test
     */
    public function usingDifferentSeparator()
    {
        $arrayFilter = new ArrayFilter('|');
        assert(
                $arrayFilter->apply($this->createParam('foo|bar')),
                equals(['foo', 'bar'])
        );
    }

    /**
     * @test
     */
    public function asArrayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = ['foo' => 'bar'];
        assert(
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asArray(),
                equals($default)
        );
    }

    /**
     * @test
     */
    public function asArrayReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asArray());
    }

    /**
     * @test
     */
    public function asArrayAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asArray();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function asArrayReturnsEmptyArrayIfParamIsEmpty()
    {
        assertEmptyArray($this->readParam('')->asArray());
    }

    /**
     * @test
     */
    public function asArrayReturnsValidValue()
    {
        assert($this->readParam('foo, bar')->asArray(), equals(['foo', 'bar']));

    }

    /**
     * @test
     */
    public function asArrayReturnsValidValueWithDifferentSeparator()
    {
        assert($this->readParam('foo|bar')->asArray('|'), equals(['foo', 'bar']));

    }
}
