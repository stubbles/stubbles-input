<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use bovigo\callmap\ClassProxy;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\input\filter\range\Range;
use stubbles\values\Value;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Tests for stubbles\input\filter\RangeFilter.
 */
#[Group('filter')]
class RangeFilterTest extends FilterTestBase
{
    private RangeFilter $rangeFilter;
    private NumberFilter&ClassProxy $filter;
    private Range&ClassProxy $range;

    protected function setUp(): void
    {
        $this->filter      = NewInstance::of(NumberFilter::class);
        $this->range       = NewInstance::of(Range::class);
        $this->rangeFilter = new RangeFilter($this->filter, $this->range);
    }

    protected function createParam(mixed $value): Value
    {
        $param = parent::createParam($value);
        $this->filter->returns(['apply' => [$value, []]]);
        return $param;
    }

    #[Test]
    public function returnsNullIfDecoratedFilterReturnsNull(): void
    {
        assertNull($this->rangeFilter->apply($this->createParam(null))[0]);
        verify($this->range, 'contains')->wasNeverCalled();
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        $this->range->returns(['contains' => true]);
        assertThat(
            $this->rangeFilter->apply($this->createParam(303))[0],
            equals(303)
        );
        verify($this->range, 'errorsOf')->wasNeverCalled();
    }

    #[Test]
    public function returnsNullIfValueNotInRange(): void
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
     * @since  2.3.1
     */
    #[Test]
    #[Group('issue41')]
    public function returnsTruncatedValueIfValueAboveMaxBorderAndTruncateAllowed(): void
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
