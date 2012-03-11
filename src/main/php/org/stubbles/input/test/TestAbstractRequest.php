<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace org\stubbles\input\test;
use net\stubbles\input\AbstractRequest;
/**
 * Helper class for the test.
 */
class TestAbstractRequest extends AbstractRequest
{
    /**
     * returns the request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return 'test';
    }
}
?>