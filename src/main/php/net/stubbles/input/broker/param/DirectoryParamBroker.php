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
use net\stubbles\input\validator\ValueReader;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Read string values based on a @Request[Directory] annotation.
 */
class DirectoryParamBroker extends MultipleSourceReaderBroker
{
    /**
     * reads single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected function read(ValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->ifIsDirectory($annotation->getBasePath(),
                                           $annotation->getDefault(),
                                           $this->allowRelative($annotation)
        );
    }

    /**
     * creates param error in case value is not set
     *
     * @param   Annotation   $annotation
     * @return  string
     */
    protected function getEmpyParamError(Annotation $annotation)
    {
        return new ParamError($annotation->getEmptyParamErrorId('DIRECTORY_NOT_EMPTY'));
    }

    /**
     * checks whether relative pathes are allowed
     *
     * @param   Annotation  $annotation
     * @return  bool
     */
    private function allowRelative(Annotation $annotation)
    {
        if ($annotation->hasValueByName('allowRelative')) {
            return $annotation->allowRelative();
        }

        return false;
    }
}
?>