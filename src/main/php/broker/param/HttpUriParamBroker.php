<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
use stubbles\peer\http\HttpUri;
/**
 * Filter http uris based on a @Request[HttpUri] annotation.
 */
class HttpUriParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation        $annotation   annotation which contains filter metadata
     * @return  \stubbles\peer\http\HttpUri
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        if ($annotation->hasValueByName('dnsCheck') && $annotation->dnsCheck()) {
            return $valueReader->asExistingHttpUri();
        }

        return $valueReader->asHttpUri();
    }

    /**
     * parses default value from annotation
     *
     * @param   string  $value
     * @return  \stubbles\peer\http\HttpUri
     */
    protected function parseDefault($value)
    {
        return HttpUri::fromString($value);
    }
}
