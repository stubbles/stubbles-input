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
use stubbles\date\span\Week;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Week] annotation.
 *
 * @since  4.5.0
 */
class WeekParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation        $annotation   annotation which contains filter metadata
     * @return  \stubbles\date\span\Week
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?Week
    {
        return $valueReader->asWeek(new DatespanRange(
                $this->createDate($annotation->getMinStartDate()),
                $this->createDate($annotation->getMaxEndDate())
        ));
    }

    /**
     * parses default value from annotation
     *
     * @param   string  $value
     * @return  \stubbles\date\span\Week
     */
    protected function parseDefault($value): Week
    {
        return Week::fromString($value);
    }

    /**
     * creates date from value
     *
     * @param   string  $value
     * @return  \stubbles\date\Date
     */
    private function createDate($value): ?Date
    {
        if (empty($value)) {
            return null;
        }

        return new Date($value);
    }
}
