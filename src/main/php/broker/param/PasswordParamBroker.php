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
use stubbles\input\ValueReader;
use stubbles\input\filter\PasswordFilter;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter passwords based on a @Request[Password] annotation.
 */
class PasswordParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  string
     */
    protected function filter(ValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asPassword($annotation->getMinDiffChars(PasswordFilter::MIN_DIFF_CHARS_DEFAULT),
                                        array()
        );
    }
}
