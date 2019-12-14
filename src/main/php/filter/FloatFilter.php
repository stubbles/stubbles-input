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
use stubbles\values\Value;
/**
 * Filters on request variables of type double / float.
 *
 * This filter takes any value and casts it to float. Afterwards its multiplied
 * with 10^$decimals to get an integer value which can be used for mathematical
 * operations for accuracy. If no value for x is given the value to filter is
 * returned as is after the cast.
 */
class FloatFilter extends Filter implements NumberFilter
{
    /**
     * number of decimals
     *
     * @var  int
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
     * apply filter on given value
     *
     * @param   \stubbles\values\Value  $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        if ($value->isNull()) {
            return $this->null();
        }

        $float = $value->value();
        settype($float, 'float');
        if (empty($this->decimals)) {
            return $this->filtered($float);
        }

        return $this->filtered((int) ($float * pow(10, $this->decimals)));
    }
}
