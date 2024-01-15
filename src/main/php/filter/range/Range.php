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
 * Interface for range definitions.
 *
 * Range definitions can be used to set up valid ranges for values.
 *
 * @since  2.0.0
 */
interface Range
{
    /**
     * checks if range contains given value
     */
    public function contains(mixed $value): bool;

    /**
     * returns list of errors when range does not contain given value
     *
     * @return  array<string,array<string,mixed>>
     */
    public function errorsOf(mixed $value): array;

    /**
     * checks whether value can be truncated to maximum value
     *
     * @return  bool
     * @since   2.3.1
     */
    public function allowsTruncate(mixed $value): bool;

    /**
     * truncates given value to max border
     *
     * @since   2.3.1
     */
    public function truncateToMaxBorder(string $value): string;
}
