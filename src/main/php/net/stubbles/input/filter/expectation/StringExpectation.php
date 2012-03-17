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
class StringExpectation extends ValueExpectation implements Range
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
     * creates an expectation where a value is required
     *
     * @return  StringExpectation
     */
    public static function createAsRequired()
    {
        return new self(true);
    }

    /**
     * creates an expectation where no value is required
     *
     * @return  StringExpectation
     */
    public static function create()
    {
        return new self(false);
    }

    /**
     * use default value if no value available
     *
     * @param   string  $default
     * @return  StringExpectation
     */
    public function useDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * sets minimum value
     *
     * @param   int  $minLength
     * @return  StringExpectation
     */
    public function minLength($minLength)
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * sets maximum value
     *
     * @param   int  $maxLength
     * @return  StringExpectation
     */
    public function maxLength($maxLength)
    {
        $this->maxLength = $maxLength;
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