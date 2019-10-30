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
use stubbles\values\Secret;
use stubbles\values\Value;
/**
 * Class for filtering secrets.
 *
 * @since  3.0.0
 */
class SecretFilter extends Filter
{
    use ReusableFilter;

    /**
     * apply filter on given value
     *
     * @param   \stubbles\values\Value    $value
     * @return  array
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->filtered(Secret::forNull());
        }

        return $this->filtered(Secret::create($value->value()));
    }
}
