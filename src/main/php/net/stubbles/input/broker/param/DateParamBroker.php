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
use net\stubbles\input\filter\expectation\DateExpectation;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Filter[DateFilter] annotation.
 */
class DateParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  net\stubbles\lang\types\Date
     */
    protected function filter(ValueFilter $valueFilter, Annotation $annotation)
    {
        return $valueFilter->asDate(DateExpectation::fromAnnotation($annotation));
    }
}
?>