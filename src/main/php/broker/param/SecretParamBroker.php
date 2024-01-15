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
use stubbles\input\filter\range\SecretMinLength;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
use stubbles\values\Secret;

/**
 * Filter boolean values based on a @Request[Secret] annotation.
 *
 * @since  3.0.0
 */
class SecretParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?Secret
    {
        if ($annotation->hasMinLength()) {
            return $valueReader->asSecret(new SecretMinLength($annotation->getMinLength()));
        }

        return $valueReader->asSecret();
    }

    #[Override]
    protected function supportsDefault(): bool
    {
        return false;
    }
}
