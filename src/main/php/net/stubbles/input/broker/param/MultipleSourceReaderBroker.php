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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker to be used to read parameters based on annotations.
 */
abstract class MultipleSourceReaderBroker extends MultipleSourceParamBroker
{
    /**
     * returns type: filter or read
     *
     * @return  string
     */
    protected function getBrokerType()
    {
        return 'read';
    }

    /**
     * reads single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected abstract function read(ValueReader $valueReader, Annotation $annotation);
}
?>