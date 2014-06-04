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
        $this->assertFalse($this->params->has('doesNotExist'));
    }

    /**
     * @test
     */
    public function returnsTrueIfParamDoesExist()
    {
        $this->assertTrue($this->params->has('foo'));
    }

    /**
     * @test
     */
    public function returnsNullParamIfParamDoesNotExist()
    {
        $this->assertTrue($this->params->get('doesNotExist')->isNull());
    }

    /**
     * @test
     */
    public function returnsParamWithValueIfParamExists()
    {
        $this->assertEquals('bar', $this->params->get('foo')->value());
    }

    /**
     * @test
     */
    public function returnsNullValueIfParamDoesNotExist()
    {
        $this->assertNull($this->params->value('doesNotExist'));
    }

    /**
     * @test
     */
    public function returnsValueIfParamExists()
    {
        $this->assertEquals('bar', $this->params->value('foo'));
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        $this->assertEquals(['foo', 'baz'], $this->params->names());
    }

    /**
     * @test
     */
    public function listOfParamErrorsIsInitiallyEmpty()
    {
        $this->assertFalse($this->params->errors()->exist());
    }

    /**
     * @test
     */
    public function paramsCanBeCounted()
    {
        $this->assertEquals(2, count($this->params));
    }

    /**
     * @test
     */
    public function canIterateOverParams()
    {
        $i = 0;
        foreach ($this->params as $paramName => $paramValue) {
            if (0 === $i) {
                $this->assertEquals('foo', $paramName);
                $this->assertEquals('bar', $paramValue);
            } else {
                $this->assertEquals('baz', $paramName);
                $this->assertEquals('value', $paramValue);
            }

            $i++;
        }
    }
}
