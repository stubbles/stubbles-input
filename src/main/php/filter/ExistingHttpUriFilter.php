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
use stubbles\values\Value;
/**
 * Class for filtering strings for valid HTTP URIs.
 *
 * Return value is null in the following cases:
 * - Given param value is null or empty string.
 * - Given param value contains an invalid http uri.
 * - Given http uri doesn't have a DNS record but DNS record is enforced.
 * In all other cases an instance of stubbles\peer\http\HttpUri is returned.
 *
 * @since  3.0.0
 */
class ExistingHttpUriFilter extends HttpUriFilter
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
        list($httpUri, $errors) = parent::apply($value);
        if (count($errors) > 0) {
            return $this->errors($errors);
        }

        if (null === $httpUri) {
            return $this->null();
        }

        if (!$httpUri->hasDnsRecord()) {
            return $this->error('HTTP_URI_NOT_AVAILABLE');
        }

        return $this->filtered($httpUri);
    }
}
