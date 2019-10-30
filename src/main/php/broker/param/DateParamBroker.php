<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\date\Date;
use stubbles\input\filter\range\DateRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Date] annotation.
 */
class DateParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation        $annotation   annotation which contains filter metadata
     * @return  \stubbles\date\Date
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asDate(new DateRange(
                $this->createDate($annotation->getMinDate()),
                $this->createDate($annotation->getMaxDate())
        ));
    }

    /**
     * parses default value from annotation
     *
     * @param   string  $value
     * @return  \stubbles\date\Date
     */
    protected function parseDefault($value)
    {
        return $this->createDate($value);
    }

    /**
     * creates date from value
     *
     * @param   string  $value
     * @return  \stubbles\date\Date
     */
    private function createDate($value)
    {
        if (empty($value)) {
            return null;
        }

        return new Date($value);
    }
}
