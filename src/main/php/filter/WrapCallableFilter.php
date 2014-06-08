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
use stubbles\input\Filter;
use stubbles\input\Param;
/**
 * Wraps a callable as filter.
 *
 * @since  3.0.0
 */
class WrapCallableFilter implements Filter
{
    /**
     * actual filter logic
     *
     * @type  callable
     */
    private $filter;

    /**
     * constructor
     *
     * @param  callable  $filter
     */
    public function __construct(callable $filter)
    {
        $this->filter = $filter;
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  mixed
     */
    public function apply(Param $param)
    {
        $filter = $this->filter;
        return $filter($param);
    }
}
