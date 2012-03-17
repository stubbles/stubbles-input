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
/**
 * Description of a number expectation.
 *
 * @api
 * @since  2.0.0
 */
class NumberExpectation extends ValueExpectation implements Range
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
     * creates an expectation where a value is required
     *
     * @return  NumberExpectation
     */
    public static function createAsRequired()
    {
        return new self(true);
    }

    /**
     * creates an expectation where no value is required
     *
     * @return  NumberExpectation
     */
    public static function create()
    {
        return new self(false);
    }

    /**
     * use default value if no value available
     *
     * @param   number  $default
     * @return  NumberExpectation
     */
    public function useDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * sets minimum value
     *
     * @param   number  $minValue
     * @return  NumberExpectation
     */
    public function minValue($minValue)
    {
        $this->minValue = $minValue;
        return $this;
    }

    /**
     * sets maximum value
     *
     * @param   number  $minValue
     * @return  NumberExpectation
     */
    public function maxValue($maxValue)
    {
        $this->maxValue = $maxValue;
        return $this;
    }

    /**
     * sets range in which value is expected
     *
     * @param   number  $min
     * @param   number  $max
     * @return  NumberExpectation
     */
    public function inRange($minValue, $maxValue)
    {
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        return $this;
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