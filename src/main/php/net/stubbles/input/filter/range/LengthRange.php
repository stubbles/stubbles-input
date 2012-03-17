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
use net\stubbles\input\error\ParamError;
use net\stubbles\lang\BaseObject;
/**
 * Range definition for length.
 *
 * @since  2.0.0
 */
class LengthRange extends BaseObject implements Range
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
     * @param  int  $minLength  minimum length
     * @param  int  $maxLength  maximum length
     */
    public function __construct($minLength, $maxLength)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * creates length range with lower border only
     *
     * @param   int  $minLength
     * @return  LengthRange
     */
    public static function minOnly($minLength)
    {
        return new self($minLength, null);
    }

    /**
     * creates length range with upper border only
     *
     * @param   int  $maxLength
     * @return  LengthRange
     */
    public static function maxOnly($maxLength)
    {
        return new self(null, $maxLength);
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