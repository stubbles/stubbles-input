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
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  \stubbles\peer\http\HttpUri
     */
    public function apply(Param $param)
    {
        $httpUri = parent::apply($param);
        if (null === $httpUri) {
            return null;
        }

        if (!$httpUri->hasDnsRecord()) {
            $param->addError('HTTP_URI_NOT_AVAILABLE');
            return null;
        }

        return $httpUri;
    }
}
