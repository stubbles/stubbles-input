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
 * Validator to ensure that a string is not shorter than a given minimum length.
 *
 * @api
 */
class MinLengthValidator extends BaseObject implements Validator
{
    /**
     * the minimum length to use for validation
     *
     * @type  int
     */
    private $minLength;

    /**
     * constructor
     *
     * @param  int  $minLength  minimum length
     */
    public function __construct($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * returns the minimum length to use for validation
     *
     * @return  int
     */
    public function getValue()
    {
        return $this->minLength;
    }

    /**
     * validate that the given value is not shorter than the minimum length
     *
     * @param   string  $value
     * @return  bool    true if value is not shorter than minimum length, else false
     */
    public function validate($value)
    {
        return (iconv_strlen($value) >= $this->minLength);
    }
}
?>