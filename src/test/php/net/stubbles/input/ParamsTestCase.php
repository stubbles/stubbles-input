<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input;
/**
 * Tests for net\stubbles\input\Params.
 *
 * @group  core
 */
class ParamsTestCase extends \PHPUnit_Framework_TestCase
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
        $this->params = new Params(array('foo' => 'bar', 'baz' => 'value'));
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
        $this->assertEquals('bar', $this->params->get('foo')->getValue());
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        $this->assertEquals(array('foo', 'baz'), $this->params->getNames());
    }

    /**
     * @test
     */
    public function listOfParamErrorsIsInitiallyEmpty()
    {
        $this->assertFalse($this->params->errors()->exist());
    }
}
?>