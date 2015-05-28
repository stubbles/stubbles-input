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
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter enum instances based on a @Request[Enum] annotation.
 *
 * @since  5.0.0
 */
class EnumParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\lang\reflect\annotation\Annotation   $annotation   annotation which contains filter metadata
     * @return  \stubbles\lang\Enum
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asEnum($annotation->getEnumClass());
    }
}