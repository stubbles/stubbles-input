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
 * Tests for net\stubbles\input\Param.
 *
 * @group  core
 */
class FilterErrorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function returnsGivenName()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals('foo', $param->getName());
    }

    /**
     * @test
     */
    public function returnsGivenValue()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals('bar', $param->getValue());
    }

    /**
     * @test
     */
    public function isNullIfValueIsNull()
    {
        $param = new Param('foo', null);
        $this->assertTrue($param->isNull());
    }

    /**
     * @test
     */
    public function isEmptyIfValueIsNull()
    {
        $param = new Param('foo', null);
        $this->assertTrue($param->isEmpty());
    }

    /**
     * @test
     */
    public function isEmptyIfValueIsEmptyString()
    {
        $param = new Param('foo', '');
        $this->assertTrue($param->isEmpty());
    }

    /**
     * @test
     */
    public function returnsValueLength()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals(3, $param->length());
    }

    /**
     * @test
     */
    public function hasNoErrorByDefault()
    {
        $param = new Param('foo', 'bar');
        $this->assertFalse($param->hasErrors());
    }

    /**
     * @test
     */
    public function hasEmptyErrorListByDefault()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals(array(), $param->getErrors());
    }

    /**
     * @test
     */
    public function hasErrorIfAdded()
    {
        $param = new Param('foo', 'bar');
        $param->addErrorWithId('SOME_ERROR');
        $this->assertTrue($param->hasErrors());
    }

    /**
     * @test
     */
    public function hasNonEmptyErrorListIfErrorAdded()
    {
        $param = new Param('foo', 'bar');
        $error = $param->addErrorWithId('SOME_ERROR', array('some' => 'detail'));
        $this->assertEquals(array('SOME_ERROR' => $error), $param->getErrors());
    }

}
?>