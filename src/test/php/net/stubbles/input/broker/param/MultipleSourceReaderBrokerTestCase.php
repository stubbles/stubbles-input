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
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Base tests for net\stubbles\input\broker\param\MultipleSourceReaderBroker.
 */
abstract class MultipleSourceReaderBrokerTestCase extends MultipleSourceParamBrokerTestCase
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
     * returns broker value
     *
     * @return  ValueReader
     */
    protected function getBrokerValue($value)
    {
        return ValueReader::mockForValue($value);
    }

}
?>