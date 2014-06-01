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
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\RuntimeException;
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
     * whether string can be truncated to maximum length
     *
     * @type  bool
     */
    private $allowsTruncate = false;

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
     * create instance which treats above max border not as error, but will lead
     * to a truncated value only
     *
     * @param   int  $minLength
     * @param   int  $maxLength
     * @return  StringLength
     * @throws  IllegalArgumentException
     * @since   2.3.1
     */
    public static function truncate($minLength, $maxLength)
    {
        if (0 >= $maxLength) {
            throw new IllegalArgumentException('Max length must be greater than 0, otherwise truncation doesn\'t make sense');
        }

        $self = new self($minLength, $maxLength);
        $self->allowsTruncate = true;
        return $self;
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
     * checks whether string can be truncated to maximum length
     *
     * @return  bool
     * @since   2.3.1
     */
    public function allowsTruncate()
    {
        return $this->allowsTruncate;
    }

    /**
     * truncates given value to max length
     *
     * @param   string  $value
     * @return  string
     * @throws  RuntimeException
     * @since   2.3.1
     */
    public function truncateToMaxBorder($value)
    {
        if ($this->allowsTruncate()) {
            return substr($value, 0, $this->maxLength);
        }

        throw new RuntimeException('Truncate value to max length not allowed');
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
