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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Datespan;
/**
 * Description of a datespan range.
 *
 * @api
 * @since  2.0.0
 */
class DatespanRange extends BaseObject implements Range
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
     * @param  Date  $minDate
     * @param  Date  $maxDate
     */
    public function __construct(Date $minDate = null, Date $maxDate = null)
    {
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;
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