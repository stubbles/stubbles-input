<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\input\Filter;
/**
 * Trait for filters which can be reused.
 *
 * @since  3.0.0
 */
trait ReusableFilter
{
    /**
     * reusable instance
     *
     * @type  \stubbles\input\Filter
     */
    private static $instance;

    /**
     * returns reusable filter instance
     *
     * @return  self
     */
    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
