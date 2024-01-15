<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter\range;

use BadMethodCallException;

/**
 * Trait for ranges that don't support truncation.
 *
 * @since  3.0.0
 */
trait NonTruncatingRange
{
    /**
     * checks whether value can be truncated to maximum value
     */
    public function allowsTruncate(mixed $value): bool
    {
        return false;
    }

    /**
     * truncates given value to max border, which is not supported for numbers
     *
     * @throws  BadMethodCallException
     */
    public function truncateToMaxBorder(string $value): string
    {
        throw new BadMethodCallException('Truncating is not supported');
    }
}