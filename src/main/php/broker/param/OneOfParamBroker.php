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
use stubbles\lang\exception\RuntimeException;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter values based on a @Request[OneOf] annotation.
 */
class OneOfParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   CommonValueReader  $valueReader  instance to filter value with
     * @param   Annotation         $annotation   annotation which contains filter metadata
     * @return  string
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->ifIsOneOf($this->getAllowedValues($annotation));
    }

    /**
     * reads default value
     *
     * @param   Annotation  $annotation
     * @return  string[]
     * @throws  RuntimeException
     */
    private function getAllowedValues(Annotation $annotation)
    {
        if ($annotation->hasValueByName('allowed')) {
            return array_map('trim', explode('|', $annotation->getAllowed()));
        }

        throw new RuntimeException('No list of allowed values in annotation @Request[OneOf] on ' . $annotation->targetName());
    }
}
