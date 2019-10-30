<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\input\filter\range\NumberRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter float values based on a @Request[Float] annotation.
 */
class FloatParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation        $annotation   annotation which contains filter metadata
     * @return  float
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asFloat(
                new NumberRange(
                        $annotation->getMinValue(),
                        $annotation->getMaxValue()
                ),
                $annotation->getDecimals()
        );
    }

    /**
     * parses default value from annotation
     *
     * @param   string  $value
     * @return  float
     */
    protected function parseDefault($value): float
    {
        return (float) $value;
    }
}
