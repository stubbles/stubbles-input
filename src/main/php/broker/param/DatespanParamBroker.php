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
use stubbles\date\span\Datespan;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;

use function stubbles\date\span\parse;
/**
 * Filter boolean values based on a @Request[Datespan] annotation.
 *
 * @since  4.3.0
 */
class DatespanParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?Datespan
    {
        return $valueReader->asDatespan(new DatespanRange(
            $this->createDate($annotation->getMinStartDate()),
            $this->createDate($annotation->getMaxEndDate())
        ));
    }

    #[Override]
    protected function parseDefault(mixed $value): ?Datespan
    {
        return parse($value);
    }

    private function createDate(?string $value): ?Date
    {
        if (empty($value)) {
            return null;
        }

        return new Date($value);
    }
}
