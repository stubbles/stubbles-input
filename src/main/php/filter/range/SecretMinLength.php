<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;

use LogicException;
use stubbles\values\Secret;

use function stubbles\values\typeOf;

/**
 * Ensures a secret has a minimum length.
 *
 * @api
 * @since  8.0.0
 */
class SecretMinLength extends AbstractRange
{
    use NonTruncatingRange;

    public function __construct(private int $minLength) { }

    /**
     * checks if given value is below min border of range
     *
     * @throws LogicException
     */
    protected function belowMinBorder(mixed $value): bool
    {
        if (!$value instanceof Secret) {
            throw new LogicException(
                sprintf(
                    'Given value is not an of instance %s but of type %s',
                    Secret::class,
                    typeOf($value)
                )
            );
        }

        return $this->minLength > $value->length();
    }

    /**
     * checks if given value is above max border of range
     */
    protected function aboveMaxBorder(mixed $value): bool
    {
        return false;
    }

    /**
     * returns error details for violations of lower border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function minBorderViolation(): array
    {
        return ['STRING_TOO_SHORT' => ['minLength' => $this->minLength]];
    }

    /**
     * returns error details for violations of upper border
     *
     * @return  array<string,array<string,scalar>>
     */
    protected function maxBorderViolation(): array
    {
        return [];
    }
}