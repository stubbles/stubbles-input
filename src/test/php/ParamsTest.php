<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use PHPUnit\Framework\TestCase;
use stubbles\values\Value;

use function bovigo\assert\{
    assertThat,
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
class ParamsTest extends TestCase
{
    /**
     * instanct to test
     *
     * @var  Params
     */
    private $params;

    protected function setUp(): void
    {
        $this->params = new Params(['foo' => 'bar', 'baz' => 'value']);
    }

    /**
     * @test
     */
    public function returnsFalseIfParamDoesNotExist(): void
    {
        assertFalse($this->params->contain('doesNotExist'));
    }

    /**
     * @test
     */
    public function returnsTrueIfParamDoesExist(): void
    {
        assertTrue($this->params->contain('foo'));
    }

    /**
     * @test
     */
    public function returnsNullValueIfParamDoesNotExist(): void
    {
        assertThat($this->params->value('doesNotExist'), isSameAs(Value::of(null)));
    }

    /**
     * @test
     */
    public function returnsValueIfParamExists(): void
    {
        assertThat($this->params->value('foo'), equals(Value::of('bar')));
    }

    /**
     * @test
     */
    public function returnsListOfParamNames(): void
    {
        assertThat($this->params->names(), equals(['foo', 'baz']));
    }

    /**
     * @test
     */
    public function listOfParamErrorsIsInitiallyEmpty(): void
    {
        assertFalse($this->params->errors()->exist());
    }

    /**
     * @test
     */
    public function paramsCanBeCounted(): void
    {
        assertThat($this->params, isOfSize(2));
    }

    /**
     * @test
     */
    public function canIterateOverParams(): void
    {
        $i = 0;
        foreach ($this->params as $paramName => $paramValue) {
            if (0 === $i) {
                assertThat($paramName, equals('foo'));
                assertThat($paramValue, equals('bar'));
            } else {
                assertThat($paramName, equals('baz'));
                assertThat($paramValue, equals('value'));
            }

            $i++;
        }
    }
}
