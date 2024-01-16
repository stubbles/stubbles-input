<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\hasKey;
/**
 * Tests for stubbles\input\filter\JsonFilter.
 */
#[Group('filter')]
#[Group('json')]
class JsonFilterTest extends FilterTestBase
{
    private JsonFilter $jsonFilter;

    protected function setUp(): void
    {
        $this->jsonFilter = new JsonFilter();
        parent::setUp();
    }

    #[Test]
    public function returnsNullIfParamIsNull(): void
    {
        assertNull($this->jsonFilter->apply($this->createParam(null))[0]);
    }

    #[Test]
    public function filterValidJsonArray(): void
    {
        assertThat($this->jsonFilter->apply($this->createParam('[1]'))[0], equals([1]));
    }

    #[Test]
    public function filterValidJsonObject(): void
    {
        $obj = new \stdClass();
        $obj->id = "abc";
        assertThat(
            $this->jsonFilter->apply($this->createParam('{"id":"abc"}'))[0],
            equals($obj)
        );
    }

    #[Test]
    public function filterValidJsonRpc(): void
    {
        $phpJsonObj = new stdClass();
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

    #[Test]
    public function returnsNullForTooBigValue(): void
    {
        assertNull(
            $this->jsonFilter->apply(
                $this->createParam(str_repeat("a", 20001))
            )[0]
        );
    }

    #[Test]
    public function addsErrorToParamForTooBigValue(): void
    {
        $param = $this->createParam(str_repeat("a", 20001));
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INPUT_TOO_BIG']));
    }

    #[Test]
    public function returnsNullForInvalidJsonCurlyBraces(): void
    {
        assertNull($this->jsonFilter->apply($this->createParam('{foo]'))[0]);
    }

    #[Test]
    public function addsErrorToParamForInvalidJsonCurlyBraces(): void
    {
        $param = $this->createParam('{foo]');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INVALID']));
    }

    #[Test]
    public function returnsNullForInvalidJsonBrackets(): void
    {
        assertNull($this->jsonFilter->apply($this->createParam('[foo}'))[0]);
    }

    #[Test]
    public function addsErrorToParamForInvalidJsonBrackets(): void
    {
        $param = $this->createParam('[foo}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INVALID']));
    }

    #[Test]
    public function returnsNullForInvalidJsonStructure(): void
    {
        assertNull(
            $this->jsonFilter->apply(
                $this->createParam('{"foo":"bar","foo","bar"}')
            )[0]
        );
    }

    #[Test]
    public function addsErrorToParamForInvalidJsonStructure(): void
    {
        $param = $this->createParam('{"foo":"bar","foo","bar"}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_SYNTAX_ERROR']));
    }

    /**
     * @since  6.0.0
     */
    #[Test]
    public function errorContainsErrorCode(): void
    {
        $param = $this->createParam('{"foo":"bar","foo","bar"}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertThat(
            $errors['JSON_SYNTAX_ERROR']->details(),
            hasKey('errorCode')
        );
    }

    /**
     * @since  6.0.0
     */
    #[Test]
    public function errorContainsErrorMessage(): void
    {
        $param = $this->createParam('{"foo":"bar","foo","bar"}');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertThat(
            $errors['JSON_SYNTAX_ERROR']->details(),
            hasKey('errorMsg')
        );
    }

    #[Test]
    public function returnsNullForInvalidJsonAlthoughPhpWouldDecodeItProperly(): void
    {
        assertNull($this->jsonFilter->apply($this->createParam('"foo"'))[0]);
    }

    #[Test]
    public function addsErrorToParamForInvalidJsonAlthoughPhpWouldDecodeItProperly(): void
    {
        $param = $this->createParam('"foo"');
        list($_, $errors) = $this->jsonFilter->apply($param);
        assertTrue(isset($errors['JSON_INVALID']));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asJsonReturnsDefaultIfParamIsNullAndNotRequired(): void
    {
        $default = ['foo' => 'bar'];
        assertThat(
                $this->readParam(null)->defaultingTo($default)->asJson(),
                equals($default)
        );
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asJsonReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->asJson());
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asJsonAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->asJson();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asJsonReturnsNullIfParamIsInvalid(): void
    {
        assertNull($this->readParam('foo')->asJson());
    }

    /**
     * @since  6.0.0
     */
    #[Test]
    public function asJsonReturnsNullIfParamIsTooLong(): void
    {
        assertNull($this->readParam(json_encode('foo'))->asJson(1));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function asJsonAddsParamErrorIfParamIsInvalid(): void
    {
        $this->readParam('foo')->asJson();
        assertTrue($this->paramErrors->existFor('bar'));
    }

    #[Test]
    public function asJsonReturnsValidValue(): void
    {
        $value = ['foo', 'bar'];
        assertThat($this->readParam(json_encode($value))->asJson(), equals($value));
    }
}
