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
     */
    public function contains(mixed $value): bool
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
     * @return  array<string,array<string,mixed>>
     */
    public function errorsOf(mixed $value): array
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
     */
    abstract protected function belowMinBorder(mixed $value): bool;

    /**
     * checks if given value is above max border of range
     */
    abstract protected function aboveMaxBorder(mixed $value): bool;

    /**
     * returns error details for violations of lower border
     *
     * @return  array<string,array<string,scalar>>
     */
    abstract protected function minBorderViolation(): array;

    /**
     * returns error details for violations of upper border
     *
     * @return  array<string,array<string,scalar>>
     */
    abstract protected function maxBorderViolation(): array;
}
