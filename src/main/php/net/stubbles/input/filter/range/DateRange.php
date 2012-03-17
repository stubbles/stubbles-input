<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter\range;
use net\stubbles\input\error\ParamError;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\types\Date;
/**
 * Range definition for dates.
 *
 * @since  2.0.0
 */
class DateRange extends BaseObject implements Range
{
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
     * @param  Date  $minDate  minimum value
     * @param  Date  $maxDate  maximum value
     */
    public function __construct(Date $minDate, Date $maxDate)
    {
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;
    }

    /**
     * creates number range with lower border only
     *
     * @param   Date  $minDate
     * @return  NumberRange
     */
    public static function minOnly(Date $minDate)
    {
        $self = new self($minDate, $minDate);
        $self->maxDate = null;
        return $self;
    }

    /**
     * creates number range with upper border only
     *
     * @param   Date  $maxDate
     * @return  NumberRange
     */
    public static function maxOnly(Date $maxDate)
    {
        $self = new self($maxDate, $maxDate);
        $self->minDate = null;
        return $self;
    }

    /**
     * checks if given value is below min border of range
     *
     * @param   mixed  $value
     * @return  bool
     * @throws  RuntimeException
     */
    public function belowMinBorder($value)
    {
        if (null === $value || null === $this->minDate) {
            return false;
        }

        if (!($value instanceof Date)) {
            throw new RuntimeException('Given value must be of instance net\\stubbles\\lang\\types\\Date');
        }

        return $this->minDate->isAfter($value);
    }

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     * @throws  RuntimeException
     */
    public function aboveMaxBorder($value)
    {
        if (null === $value || null === $this->maxDate) {
            return false;
        }

        if (!($value instanceof Date)) {
            throw new RuntimeException('Given value must be of instance net\\stubbles\\lang\\types\\Date');
        }

        return $this->maxDate->isBefore($value);
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMinParamError()
    {
        return new ParamError('DATE_TOO_EARLY', array('earliestDate' => $this->minDate->asString()));
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMaxParamError()
    {
        return new ParamError('DATE_TOO_LATE', array('latestDate' => $this->maxDate->asString()));
    }
}
?>