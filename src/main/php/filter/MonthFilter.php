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
use stubbles\date\span\Month;
use stubbles\input\Filter;
use stubbles\values\Value;
/**
 * Class for filtering months.
 *
 * The following rules apply:
 * - If given value is empty the returned value is null.
 * - If given value is not a valid month the returned value is null.
 * - If given value is a valid month the returned value is an instance of
 *   stubbles\date\span\Month.
 *
 * @since  2.5.1
 */
class MonthFilter extends Filter
{
    use ReusableFilter;

    /**
     * apply filter on given value
     *
     * In case the given value can not be transformed into the target type
     * the return value is null.
     *
     * @param   \stubbles\values\Value  $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->null();
        }

        try {
            return $this->filtered(Month::fromString($value->value()));
        } catch (\InvalidArgumentException $iae) {
            return $this->error('MONTH_INVALID');
        }
    }
}
