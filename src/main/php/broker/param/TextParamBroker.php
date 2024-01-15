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
use stubbles\input\filter\range\StringLength;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter boolean values based on a @Request[Text] annotation.
 */
class TextParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?string
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
