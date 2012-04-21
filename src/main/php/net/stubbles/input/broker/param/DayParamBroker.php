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
use net\stubbles\input\filter\expectation\DatespanExpectation;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Day] annotation.
 */
class DayParamBroker extends MultipleSourceFilterBroker
{
    /**
     * handles single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  net\stubbles\lang\types\datespan\Day
     */
    protected function filter(ValueFilter $valueFilter, Annotation $annotation)
    {
        return $valueFilter->asDay(DatespanExpectation::fromAnnotation($annotation));
    }
}
?>