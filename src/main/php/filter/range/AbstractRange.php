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
/**
 * Description of a number range.
 *
 * @since  3.0.0
 */
abstract class AbstractRange implements Range
{
    /**
     * checks if range contains given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function contains($value)
    {
        if (null === $value) {
            return false;
        }

        if ($this->belowMinBorder($value)) {
            return false;
        }

        return !$this->aboveMaxBorder($value);
    }

    /**
     * returns list of errors when range does not contain given value
     *
     * @param   mixed  $value
     * @return  array
     */
    public function errorsOf($value)
    {
        if ($this->belowMinBorder($value)) {
            return $this->minBorderViolation();
        }

        if ($this->aboveMaxBorder($value)) {
            return $this->maxBorderViolation();
        }

        return [];
    }

    /**
     * checks if given value is below min border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    protected abstract function belowMinBorder($value);

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    protected abstract function aboveMaxBorder($value);

    /**
     * returns error details for violations of lower border
     *
     * @return  array
     */
    protected abstract function minBorderViolation();

    /**
     * returns error details for violations of upper border
     *
     * @return  array
     */
    protected abstract function maxBorderViolation();
}
