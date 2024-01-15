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
use stubbles\input\filter\range\NumberRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter integer values based on a @Request[Integer] annotation.
 */
class IntegerParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?int
    {
        return $valueReader->asInt(new NumberRange(
            $annotation->getMinValue(),
            $annotation->getMaxValue()
        ));
    }

    #[Override]
    protected function parseDefault(mixed $value): int
    {
        return (int) $value;
    }
}
