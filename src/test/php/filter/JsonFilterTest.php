<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\hasKey;
require_once __DIR__ . '/FilterTest.php';
/**
 * Tests for stubbles\input\filter\JsonFilter.
 *
 * @group  filter
 * @group  json
 */
class JsonFilterTest extends FilterTest
{
    /**
     * Object under test.
     *
     * @type  JsonFilter
     */
    private $jsonFilter;

    protected function setUp(): void
    {
        $this->jsonFilter = new JsonFilter();
        parent::setUp();
    }

    /**
     * @test
     */
    public function returnsNullIfParamIsNull()
    {
        assertNull($this->jsonFilter->apply($this->createParam(null))[0]);
    }

    /**
     * @test
     */
    public function filterValidJsonArray()
    {
        assertThat($this->jsonFilter->apply($this->createParam('[1]'))[0], equals([1]));
    }

    /**
     * @test
     */
    public function filterValidJsonObject()
    {
        $obj = new \stdClass();
        $obj->id = "abc";
        assertThat(
                $this->jsonFilter->apply($this->createParam('{"id":"abc"}'))[0],
                equals($obj)
        );
    }

    /**
     * @test
     */
    public function filterValidJsonRpc()
    {
        $phpJsonObj = new \stdClass();
        $phpJsonObj->method = 'add';
        $phpJsonObj->params = [1, 2];
        $phpJsonObj->id = 1;

        assertThat(
                $this->jsonFilter->apply(
                        $this->createParam('{"method":"add","params":[1,2],"id":1}')
                )[0],
                equals($phpJsonObj)
        );
    }

    /**
     * @test
     */
    public function returnsNullForTooBigValue()
    {
        assertNull(
                $this->jsonFilter->apply(
                        $this->createParam(str_repeat("a", 20001))
                )[0]
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamForTooBigValue()
    {
        $param = $this->createParam(str_repeat("a", 20001));
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INPUT_TOO_BIG']));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonCurlyBraces()
    {
        assertNull($this->jsonFilter->apply($this->createParam('{foo]'))[0]);
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonCurlyBraces()
    {
        $param = $this->createParam('{foo]');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INVALID']));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonBrackets()
    {
        assertNull($this->jsonFilter->apply($this->createParam('[foo}'))[0]);
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonBrackets()
    {
        $param = $this->createParam('[foo}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INVALID']));
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonStructure()
    {
        assertNull(
                $this->jsonFilter->apply(
                        $this->createParam('{"foo":"bar","foo","bar"}')
                )[0]
        );
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonStructure()
    {
        $param = $this->createParam('{"foo":"bar","foo","bar"}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_SYNTAX_ERROR']));
    }

    /**
     * @test
     * @since  6.0.0
     */
    public function errorContainsErrorCode()
    {
        $param = $this->createParam('{"foo":"bar","foo","bar"}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertThat(
                $errors['JSON_SYNTAX_ERROR']->details(),
                hasKey('errorCode')
        );
    }

    /**
     * @test
     * @since  6.0.0
     */
    public function errorContainsErrorMessage()
    {
        $param = $this->createParam('{"foo":"bar","foo","bar"}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertThat(
                $errors['JSON_SYNTAX_ERROR']->details(),
                hasKey('errorMsg')
        );
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJsonAlthoughPhpWouldDecodeItProperly()
    {
        assertNull($this->jsonFilter->apply($this->createParam('"foo"'))[0]);
    }

    /**
     * @test
     */
    public function addsErrorToParamForInvalidJsonAlthoughPhpWouldDecodeItProperly()
    {
        $param = $this->createParam('"foo"');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INVALID']));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = ['foo' => 'bar'];
        assertThat(
                $this->readParam(null)->defaultingTo($default)->asJson(),
                equals($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->asJson());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->asJson();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsInvalid()
    {
        assertNull($this->readParam('foo')->asJson());
    }

    /**
     * @since  6.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsTooLong()
    {
        assertNull($this->readParam(json_encode('foo'))->asJson(1));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonAddsParamErrorIfParamIsInvalid()
    {
        $this->readParam('foo')->asJson();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asJsonReturnsValidValue() {
        $value = ['foo', 'bar'];
        assertThat($this->readParam(json_encode($value))->asJson(), equals($value));
    }
}
