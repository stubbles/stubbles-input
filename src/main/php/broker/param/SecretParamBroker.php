<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\input\filter\range\SecretMinLength;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Secret] annotation.
 *
 * @since  3.0.0
 */
class SecretParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation       $annotation   annotation which contains filter metadata
     * @return  \stubbles\values\Secret|null
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        if ($annotation->hasMinLength()) {
            return $valueReader->asSecret(new SecretMinLength($annotation->getMinLength()));
        }

        return $valueReader->asSecret();
    }

    /**
     * whether a default value for this param is supported
     *
     * @return  bool
     */
    protected function supportsDefault(): bool
    {
        return false;
    }
}
