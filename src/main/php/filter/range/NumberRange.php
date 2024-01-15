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
 * @api
 * @since  2.0.0
 */
class NumberRange extends AbstractRange
{
    use NonTruncatingRange;

    public function __construct(private ?int $minValue, private ?int $maxValue = null) { }

    /**
     * checks if given value is below min border of range
     */
    protected function belowMinBorder(mixed $value): bool
    {
        if (null === $this->minValue) {
            return false;
        }

        return $value < $this->minValue;
    }

    /**
     * checks if given value is above max border of range
     */
    protected function aboveMaxBorder(mixed $value): bool
    {
        if (null === $this->maxValue) {
            return false;
        }

        return $value > $this->maxValue;
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function minBorderViolation(): array
    {
        if (null === $this->minValue) {
            return [];
        }

        return ['VALUE_TOO_SMALL' => ['minNumber' => $this->minValue]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function maxBorderViolation(): array
    {
        if (null === $this->maxValue) {
            return [];
        }

        return ['VALUE_TOO_GREAT' => ['maxNumber' => $this->maxValue]];
    }
}
