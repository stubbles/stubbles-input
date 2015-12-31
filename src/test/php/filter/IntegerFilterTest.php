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

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\IntegerFilter.
 *
 * @group  filter
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
        $integerFilter = IntegerFilter::instance();
        assert(
                $integerFilter->apply($this->createParam($value)),
                equals($expected)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsNullAndNotRequired()
    {
        assertNull($this->readParam(null)->asInt());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsDefaultIfParamIsNullAndNotRequired()
    {
        assert($this->readParam(null)->defaultingTo(303)->asInt(), equals(303));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asInt());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asInt();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam(4)->asInt(new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam(4)->asInt(new NumberRange(5, null)
        );
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asIntReturnsValidValue()
    {
        assert($this->readParam('313')->asInt(), equals(313));

    }
}
