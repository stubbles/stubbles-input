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
use net\stubbles\input\ValueReader;
use stubbles\lang\reflect\annotation\Annotation;
use stubbles\peer\http\HttpUri;
/**
 * Filter http uris based on a @Request[HttpUri] annotation.
 */
class HttpUriParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  HttpUri
     */
    protected function filter(ValueReader $valueReader, Annotation $annotation)
    {
        if ($annotation->hasValueByName('dnsCheck') && $annotation->dnsCheck()) {
            return $valueReader->asExistingHttpUri($this->getDefault($annotation));
        }

        return $valueReader->asHttpUri($this->getDefault($annotation));
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
