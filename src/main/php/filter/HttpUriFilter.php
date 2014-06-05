<?php
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
use stubbles\input\Param;
use stubbles\peer\MalformedUriException;
use stubbles\peer\http\HttpUri;
/**
 * Class for filtering strings for valid HTTP URIs.
 *
 * Return value is null in the following cases:
 * - Given param value is null or empty string.
 * - Given param value contains an invalid http uri.
 * - Given http uri doesn't have a DNS record but DNS record is enforced.
 * In all other cases an instance of stubbles\peer\http\HttpUri is returned.
 */
class HttpUriFilter implements Filter
{
    /**
     * switch whether DNS should be checked or not
     *
     * @type  bool
     */
    private $enforceDnsRecord = false;

    /**
     * enable dns check for value to filter
     *
     * @return  HttpUriFilter
     */
    public function enforceDnsRecord()
    {
        $this->enforceDnsRecord = true;
        return $this;
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  HttpUri
     */
    public function apply(Param $param)
    {
        try {
            $httpUri = HttpUri::fromString($param->value());
        } catch (MalformedUriException $murle) {
            $param->addError('HTTP_URI_INCORRECT');
            return null;
        }

        if (null === $httpUri) {
            return null;
        }

        if (true === $this->enforceDnsRecord && !$httpUri->hasDnsRecord()) {
            $param->addError('HTTP_URI_NOT_AVAILABLE');
            return null;
        }

        return $httpUri;
    }
}
