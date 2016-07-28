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
use stubbles\peer\MalformedUri;
use stubbles\peer\http\HttpUri;
use stubbles\values\Value;
/**
 * Class for filtering strings for valid HTTP URIs.
 *
 * Return value is null in the following cases:
 * - Given param value is null or empty string.
 * - Given param value contains an invalid http uri.
 * In all other cases an instance of stubbles\peer\http\HttpUri is returned.
 */
class HttpUriFilter extends Filter
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
        if ($value->isEmpty()) {
            return $this->null();
        }

        try {
            return $this->filtered(HttpUri::fromString($value->value()));
        } catch (MalformedUri $murle) {
            return $this->error('HTTP_URI_INCORRECT');
        }
    }
}
