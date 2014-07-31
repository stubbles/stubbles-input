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
use stubbles\input\filter\ArrayFilter;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter arrays based on a @Request[Array] annotation.
 */
class ArrayParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\lang\reflect\annotation\Annotation   $annotation   annotation which contains filter metadata
     * @return  array
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asArray($annotation->getSeparator(ArrayFilter::SEPARATOR_DEFAULT));
    }

    /**
     * parses default value
     *
     * @param   string  $value
     * @return  array
     */
    protected function parseDefault($value)
    {
        return array_map('trim', explode('|', $value));
    }
}
