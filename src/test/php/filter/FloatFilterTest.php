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
 * Tests for stubbles\input\filter\FloatFilter.
 *
 * @group  filter
 */
class FloatFilterTest extends FilterTest
{
    /**
     * @return  array
     */
    public function getValueResultTuples()
    {
        return [['8.4533', 8453],
                ['8.4538', 8453],
                ['8.45', 8450],
                ['8', 8000],
                [8.4533, 8453],
                [8.4538, 8453],
                [8.45, 8450],
                [8, 8000],
                [null, null],
                [true, 1000],
                [false, 0]
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
        $floatFilter = new FloatFilter();
        assertEquals(
                $expected,
                $floatFilter->setDecimals(3)
                        ->apply($this->createParam($value))
        );
    }

    /**
     * @test
     */
    public function float()
    {
        $floatFilter = new FloatFilter();
        assertEquals(
                156,
                $floatFilter->setDecimals(2)
                        ->apply($this->createParam('1.564'))
        );
    }

    /**
     * @test
     */
    public function decimalsNotSet()
    {
        $floatFilter = new FloatFilter();
        assertEquals(1.564, $floatFilter->apply($this->createParam('1.564')));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assertEquals(3.03, $this->readParam(null)->defaultingTo(3.03)->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asFloat();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam(2.5)->asFloat(new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam(2.5)->asFloat(new NumberRange(5, null));
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asFloatReturnsValidValue()
    {
        assertEquals(3.13, $this->readParam('3.13')->asFloat());
    }

    /**
     * @test
     */
    public function asFloatReturnsValidValueUsingDecimals()
    {
        assertEquals(313, $this->readParam('3.13')->asFloat(null, 2));
    }
}
