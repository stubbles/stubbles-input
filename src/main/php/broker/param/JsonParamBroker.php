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
use stubbles\input\ValueReader;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter mail addresses based on a @Request[Json] annotation.
 */
class JsonParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  string
     */
    protected function filter(ValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asJson($this->getDefault($annotation));
    }

    /**
     * reads default value
     *
     * @param   Annotation  $annotation
     * @return  mixed
     */
    private function getDefault(Annotation $annotation)
    {
        if ($annotation->hasValueByName('default')) {
            return json_decode($annotation->getDefault());
        }

        return null;
    }
}
