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
use net\stubbles\input\Validator;
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
class EqualValidator extends BaseObject implements Validator
{
    /**
     * the expected password
     *
     * @type  string
     */
    private $expected = null;

    /**
     * constructor
     *
     * @param   scalar|null  $expected
     * @throws  IllegalArgumentException
     */
    public function __construct($expected)
    {
        if (!is_scalar($expected) && null != $expected) {
            throw new IllegalArgumentException('Can only compare scalar values and null.');
        }

        $this->expected = $expected;
    }

    /**
     * validate that the given value is eqal in content and type to the expected value
     *
     * @param   scalar|null  $value
     * @return  bool         true if value is equal to expected value, else false
     */
    public function validate($value)
    {
        if ($this->expected !== $value) {
            return false;
        }

        return true;
    }
}
?>