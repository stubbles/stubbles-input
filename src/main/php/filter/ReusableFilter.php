<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
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
     * @return  \stubbles\input\Filter
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
