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
/**
 * Range filter to ensure a value is inbetween a certain range.
 */
class RangeFilter extends BaseObject implements Filter
{
    /**
     * decorated filter
     *
     * @type  NumberFilter
     */
    private $filter;
    /**
     * range definition
     *
     * @type  Range
     */
    private $range;

    /**
     * constructor
     *
     * @param  Filter  $filter  decorated filter
     * @param  Range   $range   range definition
     */
    public function __construct(Filter $filter, Range $range)
    {
        $this->filter = $filter;
        $this->range  = $range;
    }

    /**
     * utility method that wraps given filter with given range
     *
     * @param   Filter  $filter
     * @param   Range   $range
     * @return  Filter
     */
    public static function wrap(Filter $filter, Range $range = null)
    {
        if (null === $range) {
            return $filter;
        }

        return new self($filter, $range);
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  number  filtered number
     */
    public function apply(Param $param)
    {
        $value = $this->filter->apply($param);
        if (null === $value) {
            return null;
        }

        if ($this->range->belowMinBorder($value)) {
            $param->addError($this->range->getMinParamError());
            return null;
        } elseif ($this->range->aboveMaxBorder($value)) {
            $param->addError($this->range->getMaxParamError());
            return null;
        }

        return $value;
    }
}
?>