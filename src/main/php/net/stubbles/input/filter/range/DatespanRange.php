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
use net\stubbles\input\ParamError;
use net\stubbles\lang\exception\MethodNotSupportedException;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Datespan;
/**
 * Description of a datespan range.
 *
 * @api
 * @since  2.0.0
 */
class DatespanRange implements Range
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
    public function belowMinBorder($value)
    {
        if (null === $value || null === $this->minDate) {
            return false;
        }

        if (!($value instanceof Datespan)) {
            throw new RuntimeException('Given value must be of instance net\\stubbles\\lang\\types\\datespan\\Datespan');
        }

        return $this->minDate->change()->timeTo('00:00:00')->isAfter($value->getStart());
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

        if (!($value instanceof Datespan)) {
            throw new RuntimeException('Given value must be of instance net\\stubbles\\lang\\types\\datespan\\Datespan');
        }

        return $this->maxDate->change()->timeTo('23:59:59')->isBefore($value->getEnd());
    }

    /**
     * checks whether value can be truncated to maximum value
     *
     * @return  bool
     * @since   2.3.1
     */
    public function allowsTruncate()
    {
        return false;
    }

    /**
     * truncates given value to max border, which is not supported for datespans
     *
     * @param   string  $value
     * @return  string
     * @throws  MethodNotSupportedException
     * @since   2.3.1
     */
    public function truncateToMaxBorder($value)
    {
        throw new MethodNotSupportedException('Truncating a datespan is not possible');
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
