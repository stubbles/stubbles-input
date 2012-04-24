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
use net\stubbles\input\ParamError;
use net\stubbles\input\filter\ValueFilter;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Read string values based on a @Request[Directory] annotation.
 */
class DirectoryParamBroker extends MultipleSourceParamBroker
{
    /**
     * filters single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected function filter(ValueFilter $valueFilter, Annotation $annotation)
    {
        return $valueFilter->ifIsDirectory($annotation->getBasePath(),
                                           $annotation->getDefault()
        );
    }
}
?>