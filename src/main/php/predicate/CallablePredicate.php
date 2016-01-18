<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\predicate;
/**
 * Wraps a predicate evaluation into a callable.
 */
class CallablePredicate extends Predicate
{
    /**
     * @type  callable
     */
    private $predicate;

    /**
     * constructor
     *
     * @param  callable  $predicate
     */
    public function __construct(callable $predicate)
    {
        $this->predicate = $predicate;
    }

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function test($value)
    {
        $predicate = $this->predicate;
        return $predicate($value);
    }
}
