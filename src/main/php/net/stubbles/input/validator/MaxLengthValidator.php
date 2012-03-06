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
 * Validator to ensure that a string is not longer than a given maximum length.
 *
 * @api
 */
class MaxLengthValidator extends BaseObject implements Validator
{
    /**
     * the maximum length to use for validation
     *
     * @type  string
     */
    private $maxLength;

    /**
     * constructor
     *
     * @param  int  $maxLength  maximum length
     */
    public function __construct($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * returns the maximum length to use for validation
     *
     * @return  int
     */
    public function getValue()
    {
        return $this->maxLength;
    }

    /**
     * validate that the given value is not longer than the maximum length
     *
     * @param   string  $value
     * @return  bool    true if value is not longer than maximal length, else false
     */
    public function validate($value)
    {
        return (iconv_strlen($value) <= $this->maxLength);
    }

    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('maxLength' => [max_length_of_string]);
     * </code>
     *
     * @return  array  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('maxLength' => $this->maxLength);
    }
}
?>