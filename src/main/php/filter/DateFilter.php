<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\date\Date;
use stubbles\input\Filter;
use stubbles\values\Value;
/**
 * Class for filtering dates.
 *
 * The following rules apply:
 * - If given value is empty the returned value is null.
 * - If given value is not a valid date the returned value is null.
 * - If given value is a valid date the returned value is an instance of
 *   stubbles\date\Date.
 *
 * @see  http://php.net/manual/de/datetime.formats.php
 */
class DateFilter extends Filter
{
    use ReusableFilter;

    /**
     * apply filter on given value
     *
     * In case the given value can not be transformed into the target type
     * the return value is null.
     *
     * @param   \stubbles\values\Value  $value
     * @return  mixed[]
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->null();
        }

        try {
            return $this->filtered(new Date($value->value()));
        } catch (\InvalidArgumentException $iae) {
            return $this->error('DATE_INVALID');
        }
    }
}
