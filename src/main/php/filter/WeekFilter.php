<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use InvalidArgumentException;
use stubbles\date\span\Week;
use stubbles\input\Filter;
use stubbles\values\Value;
/**
 * Class for filtering weeks.
 *
 * The following rules apply:
 * - If given value is empty the returned value is null.
 * - If given value is not a valid week the returned value is null.
 * - If given value is a valid week the returned value is an instance of
 *   stubbles\date\span\Week.
 *
 * @since  4.5.0
 */
class WeekFilter extends Filter
{
    use ReusableFilter;

    /**
     * apply filter on given value
     *
     * In case the given value can not be transformed into the target type
     * the return value is null.
     *
     * @return  mixed[]
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->null();
        }

        try {
            return $this->filtered(Week::fromString($value->value()));
        } catch (InvalidArgumentException $iae) {
            return $this->error('WEEK_INVALID');
        }
    }
}
