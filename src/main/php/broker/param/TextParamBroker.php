<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\input\filter\range\StringLength;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Text] annotation.
 */
class TextParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation        $annotation   annotation which contains filter metadata
     * @return  string|null
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asText(
                new StringLength(
                        $annotation->getMinLength(),
                        $annotation->getMaxLength()
                ),
                $this->allowedTags($annotation)
        );
    }

    /**
     * returns list of allowed tags
     *
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @return  string[]
     */
    private function allowedTags(Annotation $annotation): array
    {
        if ($annotation->hasValueByName('allowedTags')) {
            return array_map('trim', explode(',', $annotation->getAllowedTags()));
        }

        return [];
    }
}
