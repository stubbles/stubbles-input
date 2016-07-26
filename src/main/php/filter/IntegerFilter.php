<?php
declare(strict_types=1);
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
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  int
     */
    public function apply(Param $param)
    {
        if ($param->isNull()) {
            return null;
        }

        $value = $param->value();
        settype($value, 'integer');
        return $value;
    }
}
