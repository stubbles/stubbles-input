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
use stubbles\input\Param;
/**
 * Basic class for filters on request variables of type integer.
 *
 * This filter takes any value and casts it to int.
 */
class IntegerFilter implements NumberFilter
{
    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  int
     */
    public function apply(Param $param)
    {
        if ($param->isNull()) {
            return null;
        }

        $value = $param->getValue();
        settype($value, 'integer');
        return $value;
    }
}
