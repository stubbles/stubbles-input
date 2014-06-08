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
use stubbles\input\filter\PasswordFilter;
use stubbles\input\filter\SimplePasswordChecker;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter passwords based on a @Request[Password] annotation.
 */
class PasswordParamBroker extends MultipleSourceParamBroker
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
        return $valueReader->asPassword(
                SimplePasswordChecker::create()
                                     ->minDiffChars($annotation->getMinDiffChars(SimplePasswordChecker::DEFAULT_MIN_DIFF_CHARS))
                                     ->minLength($annotation->getMinLength(SimplePasswordChecker::DEFAULT_MINLENGTH))
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
