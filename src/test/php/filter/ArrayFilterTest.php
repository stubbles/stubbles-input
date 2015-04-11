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
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\ArrayFilter.
 *
 * @since  2.0.0
 * @group  filter
 */
class ArrayFilterTest extends FilterTest
{
    /**
     * @return  array
     */
    public function getValueResultTuples()
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
        assertEquals(
                $expected,
                $arrayFilter->apply($this->createParam($value)));
    }

    /**
     * @test
     */
    public function usingDifferentSeparator()
    {
        $arrayFilter = new ArrayFilter('|');
        assertEquals(
                ['foo', 'bar'],
                $arrayFilter->apply($this->createParam('foo|bar')));
    }

    /**
     * @test
     */
    public function asArrayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = ['foo' => 'bar'];
        assertEquals(
                $default,
                $this->readParam(null)
                        ->defaultingTo($default)
                        ->asArray()
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
        assertEquals([], $this->readParam('')->asArray());
    }

    /**
     * @test
     */
    public function asArrayReturnsValidValue()
    {
        $value = ['foo', 'bar'];
        assertEquals($value, $this->readParam('foo, bar')->asArray());

    }

    /**
     * @test
     */
    public function asArrayReturnsValidValueWithDifferentSeparator()
    {
        $value = ['foo', 'bar'];
        assertEquals($value, $this->readParam('foo|bar')->asArray('|'));

    }
}
