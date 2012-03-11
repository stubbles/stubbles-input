<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\Param;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\types\Date;
/**
 * Date filter to check that a date is within a certain period.
 *
 * This filter takes any date and checks if it complies with the min and/or
 * the max date.
 */
class PeriodFilter extends BaseObject implements Filter
{
    /**
     * decorated filter
     *
     * @type  DateFilter
     */
    private $filter;
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
     * @param  DateFilter  $filter     decorated date filter
     * @param  Date        $minDate  minimum length
     * @param  Date        $maxDate  maximum length
     */
    public function __construct(DateFilter $filter, Date $minDate = null, Date $maxDate = null)
    {
        $this->filter  = $filter;
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  Date
     */
    public function apply(Param $param)
    {
        $value = $this->filter->apply($param);
        if (null === $value) {
            return null;
        }

        if ($this->isBeforeEarliestDate($value)) {
            $param->addErrorWithId('DATE_TOO_EARLY', array('earliestDate' => $this->minDate->asString()));
            return null;
        } elseif ($this->isAfterLatestDate($value)) {
            $param->addErrorWithId('DATE_TOO_LATE', array('latestDate' => $this->maxDate->asString()));
            return null;
        }

        return $value;
    }

    /**
     * checks if given date is before earliest date
     *
     * @param   Date  $value
     * @return  bool
     */
    private function isBeforeEarliestDate(Date $value)
    {
        if (null === $this->minDate) {
            return false;
        }

        return $this->minDate->isAfter($value);
    }

    /**
     * checks if given date is after latest date
     *
     * @param   Date  $value
     * @return  bool
     */
    private function isAfterLatestDate(Date $value)
    {
        if (null === $this->maxDate) {
            return false;
        }

        return $this->maxDate->isBefore($value);
    }
}
?>