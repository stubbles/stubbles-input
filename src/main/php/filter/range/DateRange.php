<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;
use stubbles\date\Date;
/**
 * Description of a date range.
 *
 * @api
 * @since  2.0.0
 */
class DateRange extends AbstractRange
{
    use NonTruncatingRange;
    /**
     * minimum date
     *
     * @type  \stubbles\date\Date
     */
    private $minDate;
    /**
     * maximum date
     *
     * @type  \stubbles\date\Date
     */
    private $maxDate;

    /**
     * constructor
     *
     * @param  int|string|\DateTime|\stubbles\date\Date  $minDate
     * @param  int|string|\DateTime|\stubbles\date\Date  $maxDate
     */
    public function __construct($minDate = null, $maxDate = null)
    {
        $this->minDate = (null === $minDate) ? (null) : (Date::castFrom($minDate, 'minDate'));
        $this->maxDate = (null === $maxDate) ? (null) : (Date::castFrom($maxDate, 'maxDate'));
    }

    /**
     * checks if given value is below min border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    protected function belowMinBorder($value): bool
    {
        if (null === $this->minDate) {
            return false;
        }

        return $this->minDate->isAfter(Date::castFrom($value, 'value'));
    }

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    protected function aboveMaxBorder($value): bool
    {
        if (null === $this->maxDate) {
            return false;
        }

        return $this->maxDate->isBefore(Date::castFrom($value, 'value'));
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array
     */
    protected function minBorderViolation(): array
    {
        return ['DATE_TOO_EARLY' => ['earliestDate' => $this->minDate->asString()]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array
     */
    protected function maxBorderViolation(): array
    {
        return ['DATE_TOO_LATE' => ['latestDate' => $this->maxDate->asString()]];
    }
}
