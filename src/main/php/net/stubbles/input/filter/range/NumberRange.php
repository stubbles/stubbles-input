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
/**
 * Description of a number range.
 *
 * @api
 * @since  2.0.0
 */
class NumberRange extends BaseObject implements Range
{
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
     * @param  int  $minValue
     * @param  int  $maxValue
     */
    public function __construct($minValue, $maxValue)
    {
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    /**
     * checks if given value is below min border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function belowMinBorder($value)
    {
        if (null === $this->minValue) {
            return false;
        }

        return ($value < $this->minValue);
    }

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function aboveMaxBorder($value)
    {
        if (null === $this->maxValue) {
            return false;
        }

        return ($value > $this->maxValue);
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMinParamError()
    {
        return new ParamError('VALUE_TOO_SMALL', array('minNumber' => $this->minValue));
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMaxParamError()
    {
        return new ParamError('VALUE_TOO_GREAT', array('maxNumber' => $this->maxValue));
    }
}
?>