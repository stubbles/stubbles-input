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
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for validating that something is equal.
 *
 * This class can compare any scalar value with an expected value. The
 * value to validate has to be of the same type and should have the same
 * content as the expected value.
 *
 * @api
 */
class ContainsValidator extends BaseObject implements Validator
{
    /**
     * the scalar value to be contained in value to validate
     *
     * @type  string
     */
    private $contained = null;

    /**
     * constructor
     *
     * @param   scalar|null  $contained
     * @throws  IllegalArgumentException
     */
    public function __construct($contained)
    {
        if (!is_scalar($contained)) {
            throw new IllegalArgumentException('Can only check scalar values.');
        }

        $this->contained = $contained;
    }

    /**
     * validate that the given value is eqal in content and type to the expected value
     *
     * @param   scalar|null  $value
     * @return  bool         true if value is equal to expected value, else false
     */
    public function validate($value)
    {
        if (!is_scalar($value) || null === $value) {
            return false;
        }

        if (is_bool($this->contained)) {
            return ($value === $this->contained);
        }

        if ($value === $this->contained || false !== strpos($value, (string) $this->contained)) {
            return true;
        }

        return false;
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('contained' => [contained_value]);
     * </code>
     *
     * @return  array  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('contained' => $this->contained);
    }
}
?>