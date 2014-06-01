<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
require_once __DIR__ . '/FilterTestCase.php';
/**
 * Tests for net\stubbles\input\filter\JsonFilter.
 *
 * @package  filter
 */
class JsonFilterTestCase extends FilterTestCase
{
    /**
     * Object under test.
     *
     * @type  JsonFilter
     */
    private $jsonFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->jsonFilter = new JsonFilter();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsNullIfParamIsNull()
    {
        $this->assertNull($this->jsonFilter->apply($this->createParam(null)));
    }

    /**
     * @test
     */
    public function filterValidJsonArray()
    {
        $this->assertEquals(array(1), $this->jsonFilter->apply($this->createParam('[1]')));
    }

    /**
     * @test
     */
    public function filterValidJsonObject()
    {
        $obj = new \stdClass();
        $obj->id = "abc";
        $this->assertEquals($obj, $this->jsonFilter->apply($this->createParam('{"id":"abc"}')));
    }

    /**
     * @test
     */
    public function filterValidJsonRpc()
    {
        $phpJsonObj = new \stdClass();
        $phpJsonObj->method = 'add';
        $phpJsonObj->params = array(1, 2);
        $phpJsonObj->id = 1;

        $this->assertEquals($phpJsonObj,
                            $this->jsonFilter->apply($this->createParam('{"method":"add","params":[1,2],"id":1}'))
        );
    }

    /**
     * @test
     */
    public function returnsNullForTooBigValue()
    {
        $this->assertNull($this->jsonFilter->apply($this->createParam(str_repeat("a", 20001))));
    }

    /**
     * @test
     */
    public function addsErrorToParamForTooBigValue()
    {
        $param = $this->createParam(str_repeat("a", 20001));
        $this->jsonFilter->apply($param);
        $this->assertTrue($param->hasError('JSON_INPUT_TOO_BIG'));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonCurlyBraces()
    {
        $this->assertNull($this->jsonFilter->apply($this->createParam('{foo]')));
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonCurlyBraces()
    {
        $param = $this->createParam('{foo]');
        $this->jsonFilter->apply($param);
        $this->assertTrue($param->hasError('JSON_INVALID'));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonBrackets()
    {
        $this->assertNull($this->jsonFilter->apply($this->createParam('[foo}')));
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonBrackets()
    {
        $param = $this->createParam('[foo}');
        $this->jsonFilter->apply($param);
        $this->assertTrue($param->hasError('JSON_INVALID'));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonStructure()
    {
        $this->assertNull($this->jsonFilter->apply($this->createParam('{"foo":"bar","foo","bar"}')));
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonStructure()
    {
        $param = $this->createParam('{"foo":"bar","foo","bar"}');
        $this->jsonFilter->apply($param);
        $this->assertTrue($param->hasError('JSON_SYNTAX_ERROR'));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonAlthoughPhpWouldDecodeItProperly()
    {
        $this->assertNull($this->jsonFilter->apply($this->createParam('"foo"')));
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonAlthoughPhpWouldDecodeItProperly()
    {
        $param = $this->createParam('"foo"');
        $this->jsonFilter->apply($param);
        $this->assertTrue($param->hasError('JSON_INVALID'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = array('foo' => 'bar');
        $this->assertEquals($default,
                            $this->createValueReader(null)->asJson($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->asJson());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->asJson();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueReader('foo')->asJson());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueReader('foo')->asJson();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asJsonReturnsValidValue() {
        $value = array('foo', 'bar');
        $this->assertEquals($value, $this->createValueReader(json_encode($value))->asJson());
    }
}
