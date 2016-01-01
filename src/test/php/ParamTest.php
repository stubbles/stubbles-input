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
use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
        assert($param->name(), equals('foo'));
    }

    /**
     * @test
     */
    public function returnsGivenValue()
    {
        $param = new Param('foo', 'bar');
        assert($param->value(), equals('bar'));
    }

    /**
     * @test
     */
    public function isNullIfValueIsNull()
    {
        $param = new Param('foo', null);
        assertTrue($param->isNull());
    }

    /**
     * @test
     */
    public function isEmptyIfValueIsNull()
    {
        $param = new Param('foo', null);
        assertTrue($param->isEmpty());
    }

    /**
     * @test
     */
    public function isEmptyIfValueIsEmptyString()
    {
        $param = new Param('foo', '');
        assertTrue($param->isEmpty());
    }

    /**
     * @test
     */
    public function returnsValueLength()
    {
        $param = new Param('foo', 'bar');
        assert($param->length(), equals(3));
    }

    /**
     * @test
     */
    public function hasNoErrorByDefault()
    {
        $param = new Param('foo', 'bar');
        assertFalse($param->hasErrors());
    }

    /**
     * @test
     */
    public function hasEmptyErrorListByDefault()
    {
        $param = new Param('foo', 'bar');
        assertEmptyArray($param->errors());
    }

    /**
     * @test
     */
    public function hasErrorIfAddedWithId()
    {
        $param = new Param('foo', 'bar');
        $param->addError('SOME_ERROR');
        assertTrue($param->hasErrors());
    }

    /**
     * @test
     */
    public function hasNonEmptyErrorListIfErrorAddedWithIdAndDetails()
    {
        $param = new Param('foo', 'bar');
        $error = $param->addError('SOME_ERROR', ['some' => 'detail']);
        assert($param->errors(), equals(['SOME_ERROR' => $error]));
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
        assertTrue($param->hasErrors());
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
        assert($param->errors(), equals(['SOME_ERROR' => $error]));
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
