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
use bovigo\callmap\NewInstance;
use stubbles\input\Filter;
use stubbles\input\filter\range\Range;

use function bovigo\callmap\verify;

require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\RangeFilter.
 *
 * @group  filter
 */
class RangeFilterTest extends FilterTest
{
    /**
     * instance to test
     *
     * @type  RangeFilter
     */
    private $rangeFilter;
    /**
     * mocked decorated filter
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $filter;
    /**
     * mocked range definition
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $range;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->filter      = NewInstance::of(Filter::class);
        $this->range       = NewInstance::of(Range::class);
        $this->rangeFilter = new RangeFilter($this->filter, $this->range);
    }

    /**
     * creates param
     *
     * @param   mixed $value
     * @return  Param
     */
    protected function createParam($value)
    {
        $param = parent::createParam($value);
        $this->filter->mapCalls(['apply' => $value]);
        return $param;
    }

    /**
     * @test
     */
    public function returnsNullIfDecoratedFilterReturnsNull()
    {
        assertNull($this->rangeFilter->apply($this->createParam(null)));
        verify($this->range, 'contains')->wasNeverCalled();
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->range->mapCalls(['contains' => true]);
        assertEquals(
                303,
                $this->rangeFilter->apply($this->createParam(303))
        );
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsNullIfValueNotInRange()
    {
        $param = $this->createParam(303);
        $this->range->mapCalls(
                ['contains'       => false,
                 'allowsTruncate' => false,
                 'errorsOf'       => ['LOWER_BORDER_VIOLATION' => []]
                ]
        );
        assertNull($this->rangeFilter->apply($param));
        assertTrue($param->hasError('LOWER_BORDER_VIOLATION'));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function returnsTruncatedValueIfValueAboveMaxBorderAndTruncateAllowed()
    {
        $this->range->mapCalls(
                ['contains'            => false,
                 'allowsTruncate'      => true,
                 'truncateToMaxBorder' => 'foo'
                ]
        );
        assertEquals(
                'foo',
                $this->rangeFilter->apply($this->createParam('foobar'))
        );
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }
}
