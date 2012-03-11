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
 * Validator to ensure that a value is not smaller than a given minimum value.
 *
 * @api
 */
class MinNumberValidator extends BaseObject implements Validator
{
    /**
     * the minimum value to use for validation
     *
     * @type  double
     */
    private $minValue;

    /**
     * constructor
     *
     * @param  int|double  $minValue  minimum value
     */
    public function __construct($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * returns the minimum value to use for validation
     *
     * @return  double
     */
    public function getValue()
    {
        return $this->minValue;
    }

    /**
     * validate that the given value is greater than or equal to the minimum value
     *
     * @param   int|double  $value
     * @return  bool        true if value is greater than or equal to minimum value, else false
     */
    public function validate($value)
    {
        return ($value >= $this->minValue);
    }
}
?>