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
use stubbles\input\filter\range\DateRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Date] annotation.
 */
class DateParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?Date
    {
        return $valueReader->asDate(new DateRange(
            $this->createDate($annotation->getMinDate()),
            $this->createDate($annotation->getMaxDate())
        ));
    }

    #[Override]
    protected function parseDefault(mixed $value): ?Date
    {
        return $this->createDate($value);
    }

    private function createDate(?string $value): ?Date
    {
        if (empty($value)) {
            return null;
        }

        return new Date($value);
    }
}
