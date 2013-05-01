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
/**
 * String length limitation.
 *
 * @api
 * @since  2.0.0
 */
class StringLength implements Range
{
    /**
     * minimum length
     *
     * @type  int
     */
    private $minLength;
    /**
     * maximum length
     *
     * @type  int
     */
    private $maxLength;

    /**
     * constructor
     *
     * @param  int  $minLength
     * @param  int  $maxLength
     */
    public function __construct($minLength, $maxLength)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * checks if given value is below min border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function belowMinBorder($value)
    {
        if (null === $this->minLength) {
            return false;
        }

        return (iconv_strlen($value) < $this->minLength);
    }

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function aboveMaxBorder($value)
    {
        if (null === $this->maxLength) {
            return false;
        }

        return (iconv_strlen($value) > $this->maxLength);
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMinParamError()
    {
        return new ParamError('STRING_TOO_SHORT', array('minLength' => $this->minLength));
    }

    /**
     * returns a param error denoting violation of min border
     *
     * @return  ParamError
     */
    public function getMaxParamError()
    {
        return new ParamError('STRING_TOO_LONG', array('maxNumber' => $this->maxLength));
    }
}
?>