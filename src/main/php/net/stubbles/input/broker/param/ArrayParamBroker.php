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
use net\stubbles\input\filter\ArrayFilter;
use net\stubbles\input\filter\ValueFilter;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter arrays based on a @Request[Array] annotation.
 */
class ArrayParamBroker extends MultipleSourceFilterBroker
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
        return $valueFilter->asArray($this->getDefault($annotation),
                                     $annotation->getSeparator(ArrayFilter::SEPARATOR_DEFAULT)
        );
    }

    /**
     * reads default value
     *
     * @param   Annotation  $annotation
     * @return  array
     */
    private function getDefault(Annotation $annotation)
    {
        if ($annotation->hasValueByName('default')) {
            return array_map('trim', explode('|', $annotation->getDefault()));
        }

        return null;
    }
}
?>