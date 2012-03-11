<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\Param;
/**
 * Base class for tests of net\stubbles\input\filter\Filter instances.
 *
 * @since  2.0.0
 */
abstract class FilterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * creates param
     *
     * @param   mixed $value
     * @return  Param
     */
    protected function createParam($value)
    {
        return new Param('test', $value);
    }
}
?>