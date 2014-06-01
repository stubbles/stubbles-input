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
use net\stubbles\input\filter\ArrayFilter;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter arrays based on a @Request[Array] annotation.
 */
class ArrayParamBroker extends MultipleSourceParamBroker
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
        return $valueReader->asArray($this->getDefault($annotation),
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
