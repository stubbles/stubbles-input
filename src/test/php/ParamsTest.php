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
namespace stubbles\input;
use stubbles\values\Value;

use function bovigo\assert\{
    assert,
    assertFalse,
    assertNull,
    assertTrue,
    predicate\equals,
    predicate\isOfSize,
    predicate\isSameAs
};
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
    public function returnsNullValueIfParamDoesNotExist()
    {
        assert($this->params->value('doesNotExist'), isSameAs(Value::of(null)));
    }

    /**
     * @test
     */
    public function returnsValueIfParamExists()
    {
        assert($this->params->value('foo'), equals(Value::of('bar')));
    }

    /**
     * @test
     */
    public function returnsListOfParamNames()
    {
        assert($this->params->names(), equals(['foo', 'baz']));
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
        assert($this->params, isOfSize(2));
    }

    /**
     * @test
     */
    public function canIterateOverParams()
    {
        $i = 0;
        foreach ($this->params as $paramName => $paramValue) {
            if (0 === $i) {
                assert($paramName, equals('foo'));
                assert($paramValue, equals('bar'));
            } else {
                assert($paramName, equals('baz'));
                assert($paramValue, equals('value'));
            }

            $i++;
        }
    }
}
