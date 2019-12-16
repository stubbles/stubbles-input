<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    public function contains($value): bool
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
     * @return  array<string,array<string,mixed>>
     */
    public function errorsOf($value): array
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
    protected abstract function belowMinBorder($value): bool;

    /**
     * checks if given value is above max border of range
     *
     * @param   mixed  $value
     * @return  bool
     */
    protected abstract function aboveMaxBorder($value): bool;

    /**
     * returns error details for violations of lower border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected abstract function minBorderViolation(): array;

    /**
     * returns error details for violations of upper border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected abstract function maxBorderViolation(): array;
}
