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
use stubbles\input\ValueReader;
use stubbles\input\errors\ParamErrors;
use stubbles\values\Value;
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
    protected function createParam($value): Value
    {
        return Value::of($value);
    }

    /**
     * helper function to create request value instance
     *
     * @param   string  $value
     * @return  ValueReader
     */
    protected function readParam($value): ValueReader
    {
        return $this->read(Value::of($value));
    }

    /**
     * helper function to create request value instance
     *
     * @param   Value  $value
     * @return  ValueReader
     */
    protected function read(Value $value): ValueReader
    {
        return new ValueReader($this->paramErrors, 'bar', $value);
    }
}
