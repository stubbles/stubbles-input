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
use stubbles\peer\http\HttpUri;
/**
 * Filter http uris based on a @Request[HttpUri] annotation.
 */
class HttpUriParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?HttpUri
    {
        if ($annotation->hasValueByName('dnsCheck') && $annotation->dnsCheck()) {
            return $valueReader->asExistingHttpUri();
        }

        return $valueReader->asHttpUri();
    }
}
