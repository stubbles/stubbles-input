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
 * Filters on request variables of type double / float.
 *
 * This filter takes any value and casts it to float. Afterwards its multiplied
 * with 10^$decimals to get an integer value which can be used for mathematical
 * operations for accuracy. If no value for x is given the value to filter is
 * returned as is after the cast.
 */
class FloatFilter implements NumberFilter
{
    /**
     * number of decimals
     *
     * @type  int
     */
    private $decimals = null;

    /**
     * sets number of decimals
     *
     * @param   int  $decimals
     * @return  \stubbles\input\filter\FloatFilter
     */
    public function setDecimals(int $decimals = null): self
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  int|float
     */
    public function apply(Param $param)
    {
        if ($param->isNull()) {
            return null;
        }

        $value = $param->value();
        settype($value, 'float');
        if (empty($this->decimals)) {
            return $value;
        }

        return (int) ($value * pow(10, $this->decimals));
    }
}
