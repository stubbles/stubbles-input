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
 * Filter values based on a @Request[OneOf] annotation.
 */
class OneOfParamBroker extends MultipleSourceParamBroker
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
        return $valueReader->ifIsOneOf($this->getAllowedValues($annotation),
                                       $annotation->getDefault()
        );
    }

    /**
     * reads default value
     *
     * @param   Annotation  $annotation
     * @return  array
     */
    private function getAllowedValues(Annotation $annotation)
    {
        if ($annotation->hasValueByName('allowed')) {
            return array_map('trim', explode('|', $annotation->getAllowed()));
        }

        return array();
    }
}