<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use stubbles\input\ValueReader;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter mail addresses based on a @Request[Mail] annotation.
 */
class MailParamBroker extends MultipleSourceParamBroker
{
    /**
     * filters single param
     *
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected function filter(ValueReader $valueReader, Annotation $annotation)
    {
        return $valueReader->asMailAddress();
    }
}
