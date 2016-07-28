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
use stubbles\date\span;
use stubbles\input\Filter;
use stubbles\values\Value;
/**
 * Class for filtering datespans.
 *
 * @since  4.3.0
 */
class DatespanFilter extends Filter
{
    use ReusableFilter;

    /**
     * apply filter on given value
     *
     * In case the given value can not be transformed into the target type
     * the return value is null.
     *
     * @param   \stubbles\values\Value  $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->null();
        }

        try {
            return $this->filtered(span\parse($value->value()));
        } catch (\InvalidArgumentException $iae) {
            return $this->error('DATESPAN_INVALID');
        }
    }
}
