<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use stubbles\input\Param;
use stubbles\input\ValueReader;
use stubbles\input\errors\ParamErrors;
/**
 * Base class for tests of stubbles\input\Filter instances.
 *
 * @since  2.0.0
 */
abstract class FilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * list of param errors
     *
     * @type  ParamErrors
     */
    protected $paramErrors;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramErrors = new ParamErrors();
    }

    /**
     * creates param
     *
     * @param   mixed $value
     * @return  Param
     */
    protected function createParam($value): Param
    {
        return new Param('test', $value);
    }

    /**
     * helper function to create request value instance
     *
     * @param   string  $value
     * @return  ValueReader
     */
    protected function readParam($value): ValueReader
    {
        return $this->read(new Param('bar', $value));
    }

    /**
     * helper function to create request value instance
     *
     * @param   Param  $param
     * @return  ValueReader
     */
    protected function read(Param $param): ValueReader
    {
        return new ValueReader($this->paramErrors, $param);
    }
}
