<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\validator;
use stubbles\input\Validator;
/**
 * Validator to validate a value against a list of allowed values.
 *
 * @api
 */
class PreSelectValidator implements Validator
{
    /**
     * list of allowed values
     *
     * @type  array
     */
    private $allowedValues = [];

    /**
     * constructor
     *
     * @param  array  $allowedValues  list of allowed values
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * validate that the given value is within a list of allowed values
     *
     * @param   mixed  $value
     * @return  bool   true if value is in list of allowed values, else false
     */
    public function validate($value)
    {
        if (!is_array($value)) {
            return in_array($value, $this->allowedValues);
        }

        foreach ($value as $val) {
            if (!in_array($val, $this->allowedValues)) {
                return false;
            }
        }

        return true;
    }
}
