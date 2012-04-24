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
        if ($annotation->hasValueByName('dnsCheck') && $annotation->dnsCheck()) {
            return $valueFilter->asExistingHttpUri($this->getDefault($annotation));
        }

        return $valueFilter->asHttpUri($this->getDefault($annotation));
    }

    /**
     * returns default value provided by annotation
     *
     * @param   Annotation  $annotation
     * @return  HttpUri
     */
    private function getDefault(Annotation $annotation)
    {
        if ($annotation->hasValueByName('default')) {
            return HttpUri::fromString($annotation->getDefault());
        }

        return null;
    }
}
?>