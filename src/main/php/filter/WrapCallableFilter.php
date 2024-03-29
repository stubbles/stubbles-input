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
 * Wraps a callable as filter.
 *
 * @since  3.0.0
 */
class WrapCallableFilter extends Filter
{
    /** @var  callable */
    private $filter;

    public function __construct(callable $filter)
    {
        $this->filter = $filter;
    }

    /**
     * apply filter on given value
     *
     * @return  mixed[]
     */
    public function apply(Value $value): array
    {
        $filter   = $this->filter;
        $errors   = [];
        $filtered = $filter($value, $errors);
        if (count($errors) > 0) {
            return $this->errors($errors);
        }

        return $this->filtered($filtered);
    }
}
