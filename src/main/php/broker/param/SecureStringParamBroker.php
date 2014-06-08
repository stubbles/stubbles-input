<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use stubbles\input\filter\range\StringLength;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[SecureString] annotation.
 *
 * @since  3.0.0
 */
class SecureStringParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   CommonValueReader  $valueReader  instance to filter value with
     * @param   Annotation         $annotation   annotation which contains filter metadata
     * @return  SecureString
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asSecureString(
                new StringLength($annotation->getMinLength(),
                                 $annotation->getMaxLength()
                )
        );
    }

    /**
     * whether a default value for this param is supported
     *
     * @return  bool
     */
    protected function supportsDefault()
    {
        return false;
    }
}
