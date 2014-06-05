<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter\range;
use stubbles\date\Date;
use stubbles\date\span\Datespan;
use stubbles\lang\exception\RuntimeException;
/**
 * Description of a datespan range.
 *
 * @api
 * @since  2.0.0
 */
class DatespanRange extends AbstractRange
{
    use NonTruncatingRange;
    /**
     * minimum date
     *
     * @type  Date
     */
    private $minDate;
    /**
     * maximum date
     *
     * @type  Date
     */
    private $maxDate;

    /**
     * constructor
     *
     * @param  int|string|\DateTime|Date  $minDate
     * @param  int|string|\DateTime|Date  $maxDate
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
     * @throws  RuntimeException
     */
    protected function belowMinBorder($value)
    {
        if (null === $this->minDate) {
            return false;
        }

        if (!($value instanceof Datespan)) {
            throw new RuntimeException('Given value must be of instance stubbles\date\span\Datespan');
        }

        return $value->startsBefore($this->minDate->change()->timeTo('00:00:00'));
    }

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     * @throws  RuntimeException
     */
    protected function aboveMaxBorder($value)
    {
        if (null === $this->maxDate) {
            return false;
        }

        if (!($value instanceof Datespan)) {
            throw new RuntimeException('Given value must be of instance stubbles\date\span\Datespan');
        }

        return $value->endsAfter($this->maxDate->change()->timeTo('23:59:59'));
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array
     */
    protected function minBorderViolation()
    {
        return ['DATE_TOO_EARLY' => ['earliestDate' => $this->minDate->asString()]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array
     */
    protected function maxBorderViolation()
    {
        return ['DATE_TOO_LATE' => ['latestDate' => $this->maxDate->asString()]];
    }
}
