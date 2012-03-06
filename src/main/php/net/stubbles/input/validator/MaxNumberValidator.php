<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\validator;
use net\stubbles\lang\BaseObject;
/**
 * Validator to ensure that a value is not greater than a given maximum value.
 *
 * @api
 */
class MaxNumberValidator extends BaseObject implements Validator
{
    /**
     * the maximum value to use for validation
     *
     * @type  string
     */
    private $maxValue;

    /**
     * constructor
     *
     * @param  int|double  $maxValue  maximum value
     */
    public function __construct($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * returns the minimum value to use for validation
     *
     * @return  double
     */
    public function getValue()
    {
        return $this->maxValue;
    }

    /**
     * validate that the given value is smaller than or equal to the maximum value
     *
     * @param   int|double  $value
     * @return  bool        true if value is smaller than or equal to maximum value, else false
     */
    public function validate($value)
    {
        return ($value <= $this->maxValue);
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('maxNumber' => [maximum_value]);
     * </code>
     *
     * @return  array  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('maxNumber' => $this->maxValue);
    }
}
?>