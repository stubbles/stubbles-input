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
use stubbles\peer\http\AcceptHeader;
use stubbles\values\Value;
/**
 * Filters accept headers.
 *
 * @since  2.0.1
 */
class AcceptFilter extends Filter
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
        if ($value->isNull() || empty($value->value())) {
            return $this->filtered(new AcceptHeader());
        }

        try {
            return $this->filtered(AcceptHeader::parse($value->value()));
        } catch (\InvalidArgumentException $iae) {
            return $this->filtered(new AcceptHeader());
        }
    }
}
