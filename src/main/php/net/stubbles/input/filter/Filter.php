<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\Param;
use net\stubbles\lang\Object;
/**
 * Interface for filter.
 *
 * Filters can be used to take request values, validate them and change them
 * into any other value.
 */
interface Filter extends Object
{
    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  mixed  filtered value
     */
    public function apply(Param $param);
}
?>