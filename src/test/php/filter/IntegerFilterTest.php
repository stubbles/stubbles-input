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
use stubbles\input\filter\range\NumberRange;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\IntegerFilter.
 *
 * @package  filter
 */
class IntegerFilterTest extends FilterTest
{
    /**
     * @return  array
     */
    public function getValueResultTuples()
    {
        return [[8, 8],
                ['8', 8],
                ['', 0],
                [null, null],
                [true, 1],
                [false, 0],
                [1.564, 1]
        ];
    }

    /**
     * @param  scalar  $value
     * @param  float   $expected
     * @test
     * @dataProvider  getValueResultTuples
     */
    public function value($value, $expected)
    {
        $integerFilter = new IntegerFilter();
        $this->assertEquals($expected,
                            $integerFilter->apply($this->createParam($value)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueReader(null)->asInt());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals(303, $this->createValueReader(null)->asInt(303));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asInt());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asInt();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader(4)->asInt(null, new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader(4)->asInt(null, new NumberRange(5, null)
        );
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asIntReturnsValidValue()
    {
        $this->assertEquals(313, $this->createValueReader('313')->asInt());

    }
}
