<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter\expectation;
use net\stubbles\input\ParamError;
use net\stubbles\input\filter\Range;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\types\Date;
/**
 * Description of a date expectation.
 *
 * @api
 * @since  2.0.0
 */
class DateExpectation extends ValueExpectation implements Range
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
     * creates an expectation where a value is required
     *
     * @return  DateExpectation
     */
    public static function createAsRequired()
    {
        return new self(true);
    }

    /**
     * creates an expectation where no value is required
     *
     * @return  DateExpectation
     */
    public static function create()
    {
        return new self(false);
    }

    /**
     * use default value if no value available
     *
     * @param   Date  $default
     * @return  DateExpectation
     */
    public function useDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * sets minimum value
     *
     * @param   Date  $minDate
     * @return  DateExpectation
     */
    public function notBefore(Date $minDate)
    {
        $this->minDate = $minDate;
        return $this;
    }

    /**
     * sets maximum value
     *
     * @param   Date  $minDate
     * @return  DateExpectation
     */
    public function notAfter($maxDate)
    {
        $this->maxDate = $maxDate;
        return $this;
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