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
use net\stubbles\input\filter\ValueFilter;
use net\stubbles\input\filter\range\StringLength;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Text] annotation.
 */
class TextParamBroker extends MultipleSourceParamBroker
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
        return $valueFilter->asText($annotation->getDefault(),
                                    new StringLength($annotation->getMinLength(),
                                                     $annotation->getMaxLength()
                                    ),
                                    $this->getAllowedTags($annotation)
        );
    }

    /**
     * returns list of allowed tags
     *
     * @param   Annotation  $annotation
     * @return  string[]
     */
    private function getAllowedTags(Annotation $annotation)
    {
        if ($annotation->hasValueByName('allowedTags')) {
            return array_map('trim', explode(',', $annotation->getAllowedTags()));
        }

        return array();
    }
}
?>