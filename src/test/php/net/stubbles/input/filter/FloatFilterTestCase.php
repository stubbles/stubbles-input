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
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for stubbles\input\filter\FloatFilter.
 *
 * @package  filter
 */
class FloatFilterTestCase extends FilterTestCase
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
        $this->assertEquals($expected,
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
        $this->assertEquals(156,
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
        $this->assertEquals(1.564, $floatFilter->apply($this->createParam('1.564')));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueReader(null)->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals(3.03, $this->createValueReader(null)->asFloat(3.03));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asFloat();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader(2.5)->asFloat(null, new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader(2.5)->asFloat(null, new NumberRange(5, null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asFloatReturnsValidValue()
    {
        $this->assertEquals(3.13, $this->createValueReader('3.13')->asFloat());

    }

    /**
     * @test
     */
    public function asFloatReturnsValidValueUsingDecimals()
    {
        $this->assertEquals(313, $this->createValueReader('3.13')->asFloat(null, null, 2));

    }
}
