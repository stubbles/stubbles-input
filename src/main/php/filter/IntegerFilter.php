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
use stubbles\input\Filter;
use stubbles\values\Value;
/**
 * Basic class for filters on request variables of type integer.
 *
 * This filter takes any value and casts it to int.
 */
class IntegerFilter extends Filter implements NumberFilter
{
    use ReusableFilter;

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

        $int = $value->value();
        settype($int, 'integer');
        return $this->filtered($int);
    }
}
