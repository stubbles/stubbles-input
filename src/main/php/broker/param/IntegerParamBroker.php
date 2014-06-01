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
use stubbles\input\filter\range\NumberRange;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter integer values based on a @Request[Integer] annotation.
 */
class IntegerParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  int
     */
    protected function filter(ValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asInt($annotation->getDefault(),
                                   new NumberRange($annotation->getMinValue(),
                                                  $annotation->getMaxValue()
                                   )
        );
    }
}
