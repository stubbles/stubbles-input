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
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Base tests for net\stubbles\input\broker\param\MultipleSourceFilterBroker.
 */
abstract class MultipleSourceFilterBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * returns type: filter or read
     *
     * @return  string
     */
    protected function getBrokerType()
    {
        return 'filter';
    }

    /**
     * returns broker value
     *
     * @return  ValueFilter
     */
    protected function getBrokerValue($value)
    {
        return ValueFilter::mockForValue($value);
    }

}
?>