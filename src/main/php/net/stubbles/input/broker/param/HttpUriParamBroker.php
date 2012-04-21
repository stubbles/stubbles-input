<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker\param;
use net\stubbles\input\filter\ValueFilter;
use net\stubbles\input\filter\expectation\ValueExpectation;
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\peer\http\HttpUri;
/**
 * Filter http uris based on a @Request[HttpUri] annotation.
 */
class HttpUriParamBroker extends MultipleSourceFilterBroker
{
    /**
     * handles single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  HttpUri
     */
    protected function filter(ValueFilter $valueFilter, Annotation $annotation)
    {
        $expect = new ValueExpectation($annotation->isRequired());
        $expect->useDefault(HttpUri::fromString($annotation->getDefault()));
        if ($annotation->hasValueByName('dnsCheck') && $annotation->dnsCheck()) {
            return $valueFilter->asExistingHttpUri($expect);
        }

        return $valueFilter->asHttpUri($expect);
    }
}
?>