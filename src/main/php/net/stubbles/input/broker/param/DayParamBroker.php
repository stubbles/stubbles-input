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
use net\stubbles\input\filter\range\DatespanRange;
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Day;
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
        return $valueFilter->asDay($this->getDefault($annotation),
                                   new DatespanRange($this->createDate($annotation->getMinStartDate()),
                                                     $this->createDate($annotation->getMaxEndDate())
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
            return new Day($annotation->getDefault());
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