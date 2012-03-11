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
 * Range filter to ensure a number is inbetween a certain range.
 *
 * This filter takes any number and checks if it complies with the min and/or
 * the max value.
 */
class RangeFilter extends BaseObject implements NumberFilter
{
    /**
     * decorated filter
     *
     * @type  NumberFilter
     */
    private $filter;
    /**
     * minimum value
     *
     * @type  number
     */
    private $minValue;
    /**
     * maximum value
     *
     * @type  number
     */
    private $maxValue;

    /**
     * constructor
     *
     * @param  NumberFilter  $filter    decorated number filter
     * @param  number        $minValue  minimum value
     * @param  number        $maxValue  maximum value
     */
    public function __construct(NumberFilter $filter, $minValue = null, $maxValue = null)
    {
        $this->filter   = $filter;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
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

        if ($this->isLesserThanMinValue($value)) {
            $param->addErrorWithId('VALUE_TOO_SMALL', array('minNumber' => $this->minValue));
            return null;
        } elseif ($this->isGreaterThanMaxValue($value)) {
            $param->addErrorWithId('VALUE_TOO_GREAT', array('maxNumber' => $this->maxValue));
            return null;
        }

        return $value;
    }

    /**
     * checks if given value is lesser than minimum value
     *
     * @param   number  $value
     * @return  bool
     */
    private function isLesserThanMinValue($value)
    {
        if (null === $this->minValue) {
            return false;
        }

        return ($value < $this->minValue);
    }

    /**
     * checks if given value is greater than maximum value
     *
     * @param   number  $value
     * @return  bool
     */
    private function isGreaterThanMaxValue($value)
    {
        if (null === $this->maxValue) {
            return false;
        }

        return ($value > $this->maxValue);
    }
}
?>