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
use net\stubbles\input\validator\ValueReader;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter mail addresses based on a @Request[Mail] annotation.
 */
class MailParamBroker extends MultipleSourceReaderBroker
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
        return $valueReader->ifIsMailAddress($annotation->getDefault());
    }
}
?>