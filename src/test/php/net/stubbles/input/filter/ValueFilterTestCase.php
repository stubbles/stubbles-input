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
use net\stubbles\input\Param;
use net\stubbles\input\ParamErrors;
use net\stubbles\input\filter\range\DateRange;
use net\stubbles\input\filter\range\DatespanRange;
use net\stubbles\input\filter\range\StringLength;
use net\stubbles\input\filter\range\NumberRange;
use net\stubbles\lang\types\Date;
use net\stubbles\lang\types\datespan\Day;
use net\stubbles\peer\http\HttpUri;
/**
 * Tests for net\stubbles\input\ValueFilter.
 *
 * @since  1.3.0
 * @group  filter
 */
class ValueFilterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * list of param errors
     *
     * @type  ParamErrors
     */
    protected $paramErrors;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramErrors = new ParamErrors();
    }

    /**
     * helper function to create request value instance
     *
     * @param   string  $value
     * @return  ValueFilter
     */
    protected function createValueFilter($value)
    {
        return $this->createValueFilterWithParam(new Param('bar', $value));
    }

    /**
     * helper function to create request value instance
     *
     * @param   Param  $param
     * @return  ValueFilter
     */
    protected function createValueFilterWithParam(Param $param)
    {
        return new ValueFilter($this->paramErrors,
                               $param
               );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asArrayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = array('foo' => 'bar');
        $this->assertEquals($default,
                            $this->createValueFilter(null)
                                 ->asArray($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asArrayReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asArray());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asArrayAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asArray();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asArrayReturnsEmptyArrayIfParamIsEmpty()
    {
        $this->assertEquals(array(), $this->createValueFilter('')->asArray());
    }

    /**
     * @test
     */
    public function asArrayReturnsValidValue()
    {
        $value = array('foo', 'bar');
        $this->assertEquals($value, $this->createValueFilter('foo, bar')->asArray());

    }

    /**
     * @test
     */
    public function asArrayReturnsValidValueWithDifferentSeparator()
    {
        $value = array('foo', 'bar');
        $this->assertEquals($value, $this->createValueFilter('foo|bar')->asArray(null, '|'));

    }

    /**
     * @since  1.7.0
     * @test
     * @group  bug266
     */
    public function asBoolReturnsDefaultIfParamIsNullAndDefaultIsNotNull()
    {
        $this->assertTrue($this->createValueFilter(null)->asBool(true));
    }

    /**
     * @since  1.7.0
     * @test
     * @group  bug266
     */
    public function asBoolReturnsFalseIfParamAndDefaultIsNotNull()
    {
        $this->assertFalse($this->createValueFilter(null)->asBool());
    }

    /**
     * @since  1.7.0
     * @test
     * @group  bug266
     */
    public function asBoolWithFalseValueReturnsFalse()
    {
        $this->assertFalse($this->createValueFilter(0)->asBool());
    }

    /**
     * @since  1.7.0
     * @test
     * @group  bug266
     */
    public function asBoolWithTrueValueReturnsTrue()
    {
        $this->assertTrue($this->createValueFilter(1)->asBool());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asInt());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals(303, $this->createValueFilter(null)->asInt(303));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asInt());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asInt();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter(4)->asInt(null, new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter(4)->asInt(null, new NumberRange(5, null)
        );
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asIntReturnsValidValue()
    {
        $this->assertEquals(313, $this->createValueFilter('313')->asInt());

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals(3.03, $this->createValueFilter(null)->asFloat(3.03));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asFloat());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asFloat();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter(2.5)->asFloat(null, new NumberRange(5, null)));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter(2.5)->asFloat(null, new NumberRange(5, null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asFloatReturnsValidValue()
    {
        $this->assertEquals(3.13, $this->createValueFilter('3.13')->asFloat());

    }

    /**
     * @test
     */
    public function asFloatReturnsValidValueUsingDecimals()
    {
        $this->assertEquals(313, $this->createValueFilter('3.13')->asFloat(null, null, 2));

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('', $this->createValueFilter(null)->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('baz',
                            $this->createValueFilter(null)->asString('baz')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asString());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asString();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')
                               ->asString(null, new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asString(null, new StringLength(5, null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asStringReturnsValidValue()
    {
        $this->assertEquals('foo', $this->createValueFilter('foo')->asString());

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsEmptyStringIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('', $this->createValueFilter(null)->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('baz', $this->createValueFilter(null)
                                        ->asText('baz')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asText());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asText();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')
                               ->asText(null, new StringLength(5, null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asText(null, new StringLength(5, null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asTextReturnsValidValue()
    {
        $this->assertEquals('foo', $this->createValueFilter('foo<b>')->asText());

    }

    /**
     * @test
     */
    public function asTextWithAllowedTagsReturnsValidValue()
    {
        $this->assertEquals('foo<b>', $this->createValueFilter('foo<b>')->asText(null, null, array('b')));

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = array('foo' => 'bar');
        $this->assertEquals($default,
                            $this->createValueFilter(null)
                                 ->asJson($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asJson());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asJson();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asJson());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asJson();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asJsonReturnsValidValue()
    {
        $value = array('foo', 'bar');
        $this->assertEquals($value, $this->createValueFilter(json_encode($value))->asJson());

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asPassword());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asPassword();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asPassword());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asPassword();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asPasswordReturnsValidValue()
    {
        $this->assertEquals('abcde', $this->createValueFilter('abcde')->asPassword());

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('http://example.com/',
                            $this->createValueFilter(null)
                                 ->asHttpUri(HttpUri::fromString('http://example.com/'))
                                 ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asHttpUri();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asHttpUri();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asHttpUriReturnsValidValue()
    {
        $this->assertEquals('http://example.com/',
                            $this->createValueFilter('http://example.com/')
                                 ->asHttpUri()
                                 ->asString()
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('http://example.com/',
                            $this->createValueFilter(null)
                                 ->asExistingHttpUri(HttpUri::fromString('http://example.com/'))
                                 ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asExistingHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asExistingHttpUri();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asExistingHttpUri());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asExistingHttpUri();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asExistingHttpUriReturnsValidValue()
    {
        $this->assertEquals('http://localhost/',
                            $this->createValueFilter('http://localhost/')
                                 ->asExistingHttpUri()
                                 ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = Date::now();
        $this->assertEquals($default,
                            $this->createValueFilter(null)
                                 ->asDate($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asDate();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asDate());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asDate();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDateReturnsValidValue()
    {
        $this->assertEquals('2012-03-11',
                            $this->createValueFilter('2012-03-11')
                                 ->asDate()
                                 ->format('Y-m-d')
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsOutOfRange()
    {
        $this->assertNull($this->createValueFilter(new Date('yesterday'))
                               ->asDate(null, new DateRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsOutOfRange()
    {
        $this->createValueFilter(new Date('yesterday'))
             ->asDate(null, new DateRange(Date::now(), null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = new Day();
        $this->assertEquals($default,
                            $this->createValueFilter(null)
                                 ->asDay($default)
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->required()->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->required()->asDay();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asDay());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asDay();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asDayReturnsValidValue()
    {
        $this->assertEquals('2012-03-11',
                            $this->createValueFilter('2012-03-11')
                                 ->asDay()
                                 ->format('Y-m-d')
        );

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayReturnsNullIfParamIsOutOfRange()
    {
        $this->assertNull($this->createValueFilter(new Day('yesterday'))
                               ->asDay(null, new DatespanRange(Date::now(), null))
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDayAddsParamErrorIfParamIsOutOfRange()
    {
        $this->createValueFilter(new Day('yesterday'))
             ->asDay(null, new DatespanRange(Date::now(), null));
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function returnsNullIfParamHasErrors()
    {
        $param = new Param('bar', 'foo');
        $param->addErrorWithId('SOME_ERROR');
        $mockFilter = $this->getMock('net\\stubbles\\input\\filter\\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->with($this->equalTo($param))
                   ->will($this->returnValue('baz'));
        $this->assertNull($this->createValueFilterWithParam($param)->withFilter($mockFilter));
    }

    /**
     * @test
     */
    public function errorListContainsParamError()
    {
        $param = new Param('bar', 'foo');
        $param->addErrorWithId('SOME_ERROR');
        $mockFilter = $this->getMock('net\\stubbles\\input\\filter\\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->with($this->equalTo($param))
                   ->will($this->returnValue('baz'));
        $this->createValueFilterWithParam($param)->withFilter($mockFilter);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'SOME_ERROR'));
    }

    /**
     * @test
     */
    public function returnsValueFromFilter()
    {
        $mockFilter = $this->getMock('net\\stubbles\\input\\filter\\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->createValueFilter('foo')->withFilter($mockFilter));
    }

    /**
     * @test
     */
    public function canBeCreatedAsMock()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\filter\\ValueFilter',
                                ValueFilter::mockForValue('bar')
        );
    }
}
?>