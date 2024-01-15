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
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter arrays based on a @Request[Array] annotation.
 */
class ArrayParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?array
    {
        return $valueReader->asArray($annotation->getSeparator(CommonValueReader::ARRAY_SEPARATOR));
    }

    #[Override]
    protected function parseDefault(mixed $value): array
    {
        return array_map('trim', explode('|', $value));
    }
}
