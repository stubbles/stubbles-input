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
 * Tests for stubbles\input\Param.
 *
 * @group  core
 */
class ParamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function returnsGivenName()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals('foo', $param->name());
    }

    /**
     * @test
     */
    public function returnsGivenValue()
    {
        $param = new Param('foo', 'bar');
        $this->assertEquals('bar', $param->value());
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
        $this->assertEquals([], $param->errors());
    }

    /**
     * @test
     */
    public function hasErrorIfAddedWithId()
    {
        $param = new Param('foo', 'bar');
        $param->addError('SOME_ERROR');
        $this->assertTrue($param->hasErrors());
    }

    /**
     * @test
     */
    public function hasNonEmptyErrorListIfErrorAddedWithIdAndDetails()
    {
        $param = new Param('foo', 'bar');
        $error = $param->addError('SOME_ERROR', ['some' => 'detail']);
        $this->assertEquals(['SOME_ERROR' => $error], $param->errors());
    }

    /**
     * @test
     * @since  2.3.3
     * @group  issue_46
     */
    public function hasErrorIfAddedAsInstance()
    {
        $param = new Param('foo', 'bar');
        $param->addError('SOME_ERROR');
        $this->assertTrue($param->hasErrors());
    }

    /**
     * @test
     * @since  2.3.3
     * @group  issue_46
     */
    public function hasNonEmptyErrorListIfErrorAddedAsInstance()
    {
        $param = new Param('foo', 'bar');
        $error = $param->addError('SOME_ERROR', ['some' => 'detail']);
        $this->assertEquals(['SOME_ERROR' => $error], $param->errors());
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Given error must either be an error id or an instance of stubbles\input\errors\ParamError
     * @since  2.3.3
     * @group  issue_46
     */
    public function addNonParamErrorAndNoErrorIdResultsInInvalidArgumentException()
    {
        $param = new Param('foo', 'bar');
        $param->addError(500);
    }
}
