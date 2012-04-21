<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker\param;
use net\stubbles\input\filter\PasswordFilter;
use net\stubbles\input\filter\ValueFilter;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter passwords based on a @Filter[PasswordFilter] annotation.
 */
class PasswordParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  string
     */
    protected function filter(ValueFilter $valueFilter, Annotation $annotation)
    {
        return $valueFilter->asPassword($annotation->getMinDiffChars(PasswordFilter::MIN_DIFF_CHARS_DEFAULT),
                                        array(),
                                        $this->isPasswordRequired($annotation)
        );
    }

    /**
     * checks if password is required
     *
     * @param   Annotation  $annotation
     * @return  bool
     */
    private function isPasswordRequired(Annotation $annotation)
    {
        if ($annotation->hasValueByName('required')) {
            return $annotation->isRequired();
        }

        return true;
    }
}
?>