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
use RuntimeException;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter values based on a @Request[OneOf] annotation.
 */
class OneOfParamBroker extends MultipleSourceParamBroker
{
    #[Override]
    protected function filter(CommonValueReader $valueReader, Annotation $annotation): ?string
    {
        return $valueReader->ifIsOneOf($this->allowedValues($annotation));
    }

    /**
     * @return  string[]
     * @throws  RuntimeException
     */
    private function allowedValues(Annotation $annotation): array
    {
        if ($annotation->hasValueByName('allowed')) {
            return array_map('trim', explode('|', $annotation->getAllowed()));
        } elseif ($annotation->hasValueByName('allowedSource')) {
            $callable = array_map('trim', explode(
              '::',
              str_replace('()', '', $annotation->getAllowedSource())
            ));
            if (!is_callable($callable)) {
              throw new RuntimeException(
                  'Defined source "' . $annotation->getAllowedSource() . '" for allowed values in @Request[OneOf] on '
                  . $annotation->target() . ' is not callable.'
              );
            }

            return call_user_func($callable);
        }

        throw new RuntimeException(
            'No list of allowed values in annotation @Request[OneOf] on '
            . $annotation->target()
        );
    }
}
