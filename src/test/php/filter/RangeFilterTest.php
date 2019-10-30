<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use bovigo\callmap\NewInstance;
use stubbles\input\Filter;
use stubbles\input\filter\range\Range;
use stubbles\values\Value;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
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

    protected function setUp(): void
    {
        $this->filter      = NewInstance::of(Filter::class);
        $this->range       = NewInstance::of(Range::class);
        $this->rangeFilter = new RangeFilter($this->filter, $this->range);
    }

    /**
     * creates param
     *
     * @param   mixed  $value
     * @return  Value
     */
    protected function createParam($value): Value
    {
        $param = parent::createParam($value);
        $this->filter->returns(['apply' => [$value, []]]);
        return $param;
    }

    /**
     * @test
     */
    public function returnsNullIfDecoratedFilterReturnsNull()
    {
        assertNull($this->rangeFilter->apply($this->createParam(null))[0]);
        verify($this->range, 'contains')->wasNeverCalled();
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->range->returns(['contains' => true]);
        assertThat(
                $this->rangeFilter->apply($this->createParam(303))[0],
                equals(303)
        );
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function returnsNullIfValueNotInRange()
    {
        $param = $this->createParam(303);
        $this->range->returns([
                'contains'       => false,
                'allowsTruncate' => false,
                'errorsOf'       => ['LOWER_BORDER_VIOLATION' => []]
        ]);
        list($result, $errors) = $this->rangeFilter->apply($param);
        assertNull($result);
        assertTrue(isset($errors['LOWER_BORDER_VIOLATION']));
    }

    /**
     * @test
     * @since  2.3.1
     * @group  issue41
     */
    public function returnsTruncatedValueIfValueAboveMaxBorderAndTruncateAllowed()
    {
        $this->range->returns([
                'contains'            => false,
                'allowsTruncate'      => true,
                'truncateToMaxBorder' => 'foo'
        ]);
        assertThat(
                $this->rangeFilter->apply($this->createParam('foobar'))[0],
                equals('foo')
        );
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }
}
