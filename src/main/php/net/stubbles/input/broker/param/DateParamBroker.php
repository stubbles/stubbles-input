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
use net\stubbles\input\filter\range\DateRange;
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\types\Date;
/**
 * Filter boolean values based on a @Request[Date] annotation.
 */
class DateParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  Date
     */
    protected function filter(ValueFilter $valueFilter, Annotation $annotation)
    {
        return $valueFilter->asDate($this->getDefault($annotation),
                                   new DateRange($this->createDate($annotation->getMinDate()),
                                                 $this->createDate($annotation->getMaxDate())
                                   )
        );
    }

    /**
     * reads default value from annotation
     *
     * @param   Annotation $annotation
     * @return  Date
     */
    private function getDefault(Annotation $annotation)
    {
        if ($annotation->hasValueByName('default')) {
            return new Date($annotation->getDefault());
        }

        return null;
    }

    /**
     * creates date from value
     *
     * @param   string  $value
     * @return  Date
     */
    private function createDate($value)
    {
        if (empty($value)) {
            return null;
        }

        return new Date($value);
    }
}
?>