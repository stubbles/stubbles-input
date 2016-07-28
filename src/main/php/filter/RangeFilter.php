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
use stubbles\input\Filter;
use stubbles\input\filter\range\Range;
use stubbles\values\Value;
/**
 * Range filter to ensure a value is inbetween a certain range.
 */
class RangeFilter extends Filter
{
    /**
     * decorated filter
     *
     * @type  \stubbles\input\filter\NumberFilter
     */
    private $filter;
    /**
     * range definition
     *
     * @type  \stubbles\input\filter\rangeRange
     */
    private $range;

    /**
     * constructor
     *
     * @param  \stubbles\input\Filter              $filter  decorated filter
     * @param  \stubbles\input\filter\range\Range  $range   range definition
     */
    public function __construct(Filter $filter, Range $range)
    {
        $this->filter = $filter;
        $this->range  = $range;
    }

    /**
     * utility method that wraps given filter with given range
     *
     * @param   \stubbles\input\Filter              $filter  decorated filter
     * @param   \stubbles\input\filter\range\Range  $range   range definition
     * @return  \stubbles\input\Filter
     */
    public static function wrap(Filter $filter, Range $range = null)
    {
        if (null === $range) {
            return $filter;
        }

        return new self($filter, $range);
    }

    /**
     * apply filter on given value
     *
     * @param   \stubbles\values\Value  $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        list($value, $errors) = $this->filter->apply($value);
        if (count($errors) > 0) {
            return $this->errors($errors);
        }

        if (null === $value) {
            return $this->null();
        }

        if ($this->range->contains($value)) {
            return $this->filtered($value);
        }

        if ($this->range->allowsTruncate($value)) {
            return $this->filtered($this->range->truncateToMaxBorder($value));
        }

        return $this->errors($this->range->errorsOf($value));
    }
}
