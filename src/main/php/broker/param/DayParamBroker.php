<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use Override;
use stubbles\date\Date;
use stubbles\date\span\Day;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Day] annotation.
 */
class DayParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?Day
    {
        return $valueReader->asDay(new DatespanRange(
            $this->createDate($annotation->getMinStartDate()),
            $this->createDate($annotation->getMaxEndDate())
        ));
    }

    #[Override]
    protected function parseDefault(mixed $value): Day
    {
        return new Day($value);
    }

    private function createDate(?string $value): ?Date
    {
        if (empty($value)) {
            return null;
        }

        return new Date($value);
    }
}
