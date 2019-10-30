<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\input\filter\range\NumberRange;

use function bovigo\assert\assertThat;
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
    public function getValueResultTuples(): array
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
        assertThat(
                $integerFilter->apply($this->createParam($value))[0],
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
        assertThat($this->readParam(null)->defaultingTo(303)->asInt(), equals(303));
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
        assertThat($this->readParam('313')->asInt(), equals(313));

    }
}
