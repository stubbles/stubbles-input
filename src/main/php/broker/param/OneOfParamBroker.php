<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter values based on a @Request[OneOf] annotation.
 */
class OneOfParamBroker extends MultipleSourceParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation        $annotation   annotation which contains filter metadata
     * @return  string
     */
    protected function filter(CommonValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->ifIsOneOf($this->allowedValues($annotation));
    }

    /**
     * reads default value
     *
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @return  string[]
     * @throws  \RuntimeException
     */
    private function allowedValues(Annotation $annotation): array
    {
        if ($annotation->hasValueByName('allowed')) {
            return array_map('trim', explode('|', $annotation->getAllowed()));
        } elseif ($annotation->hasValueByName('allowedSource')) {
            return call_user_func(array_map('trim', explode(
                    '::',
                    str_replace('()', '', $annotation->getAllowedSource())
            )));
        }

        throw new \RuntimeException(
                'No list of allowed values in annotation @Request[OneOf] on '
                . $annotation->target()
        );
    }
}
