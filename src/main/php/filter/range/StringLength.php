<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter\range;
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\RuntimeException;
/**
 * String length limitation.
 *
 * @api
 * @since  2.0.0
 */
class StringLength extends AbstractRange
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
    protected function belowMinBorder($value)
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
    protected function aboveMaxBorder($value)
    {
        if (null === $this->maxLength) {
            return false;
        }

        return (iconv_strlen($value) > $this->maxLength);
    }

    /**
     * checks whether string can be truncated to maximum length
     *
     * @param   mixed  $value
     * @return  bool
     * @since   2.3.1
     */
    public function allowsTruncate($value)
    {
        return $this->allowsTruncate && $this->aboveMaxBorder($value);
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
        if ($this->allowsTruncate($value)) {
            return substr($value, 0, $this->maxLength);
        }

        throw new RuntimeException('Truncate value to max length not allowed');
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array
     */
    protected function minBorderViolation()
    {
        return ['STRING_TOO_SHORT' => ['minLength' => $this->minLength]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array
     */
    protected function maxBorderViolation()
    {
        return ['STRING_TOO_LONG' => ['maxLength' => $this->maxLength]];
    }
}
