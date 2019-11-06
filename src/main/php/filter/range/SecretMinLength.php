<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;
use stubbles\values\Secret;
/**
 * Ensures a secret has a minimum length.
 *
 * @api
 * @since  8.0.0
 */
class SecretMinLength extends AbstractRange
{
    use NonTruncatingRange;

    /**
     * minimum length
     *
     * @type  int
     */
    private $minLength;

    /**
     * constructor
     *
     * @param  int  $minLength
     */
    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * checks if given value is below min border of range
     *
     * @param   \stubbles\values\Secret  $value
     * @return  bool
     */
    protected function belowMinBorder($value): bool
    {
        return $this->minLength > $value->length();
    }

    /**
     * checks if given value is above max border of range
     *
     * @param   \stubbles\values\Secret  $value
     * @return  bool
     */
    protected function aboveMaxBorder($value): bool
    {
        return false;
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array
     */
    protected function minBorderViolation(): array
    {
        return ['STRING_TOO_SHORT' => ['minLength' => $this->minLength]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array
     */
    protected function maxBorderViolation(): array
    {
        return [];
    }
}