<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\values\Value;
/**
 * Basic class for filters on request variables of type integer.
 *
 * This filter takes any value and casts it to int.
 */
class IntegerFilter extends NumberFilter
{
    use ReusableFilter;

    /**
     * apply filter on given value
     *
     * @param   \stubbles\values\Value  $value
     * @return  mixed[]
     */
    public function apply(Value $value): array
    {
        if ($value->isNull()) {
            return $this->null();
        }

        $int = $value->value();
        settype($int, 'integer');
        return $this->filtered($int);
    }
}
