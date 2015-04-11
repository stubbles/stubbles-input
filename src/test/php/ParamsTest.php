<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input;
/**
 * Tests for stubbles\input\Params.
 *
 * @group  core
 */
class ParamsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instanct to test
     *
     * @type  Params
     */
    private $params;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->params = new Params(['foo' => 'bar', 'baz' => 'value']);
    }

    /**
     * @test
     */
    public function returnsFalseIfParamDoesNotExist()
    {
        assertFalse($this->params->contain('doesNotExist'));
    }

    /**
     * @test
     */
    public function returnsTrueIfParamDoesExist()
    {
        assertTrue($this->params->contain('foo'));
    }

    /**
     * @test
     */
    public function returnsNullParamIfParamDoesNotExist()
    {
        assertTrue($this->params->get('doesNotExist')->isNull());
    }

    /**
     * @test
     */
    public function returnsParamWithValueIfParamExists()
    {
        assertEquals('bar', $this->params->get('foo')->value());
    }

    /**
     * @test
     */
    public function returnsNullValueIfParamDoesNotExist()
    {
        assertNull($this->params->value('doesNotExist'));
    }

    /**
     * @test
     */
    public function returnsValueIfParamExists()
    {
        assertEquals('bar', $this->params->value('foo'));
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        assertEquals(['foo', 'baz'], $this->params->names());
    }

    /**
     * @test
     */
    public function listOfParamErrorsIsInitiallyEmpty()
    {
        assertFalse($this->params->errors()->exist());
    }

    /**
     * @test
     */
    public function paramsCanBeCounted()
    {
        assertEquals(2, count($this->params));
    }

    /**
     * @test
     */
    public function canIterateOverParams()
    {
        $i = 0;
        foreach ($this->params as $paramName => $paramValue) {
            if (0 === $i) {
                assertEquals('foo', $paramName);
                assertEquals('bar', $paramValue);
            } else {
                assertEquals('baz', $paramName);
                assertEquals('value', $paramValue);
            }

            $i++;
        }
    }
}
