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
use stdClass;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter mail addresses based on a @Request[Json] annotation.
 */
class JsonParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): stdClass|array|null
    {
        return $valueReader->asJson();
    }

    #[Override]
    protected function parseDefault(mixed $value): stdClass|array|null
    {
        return json_decode($value);
    }
}
