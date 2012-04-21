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
/**
 * Filter mail addresses based on a @Request[Json] annotation.
 */
class JsonParamBroker extends MultipleSourceFilterBroker
{
    /**
     * handles single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  string
     */
    protected function filter(ValueFilter $valueFilter, Annotation $annotation)
    {
        $expect = new ValueExpectation($annotation->isRequired());
        $expect->useDefault($this->getDefault($annotation));
        return $valueFilter->asJson($expect);
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
?>