<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input;
/**
 * Interface for filter.
 *
 * Filters can be used to take request values, validate them and change them
 * into any other value.
 *
 * @api
 */
interface Filter
{
    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  mixed  filtered value
     */
    public function apply(Param $param);
}
