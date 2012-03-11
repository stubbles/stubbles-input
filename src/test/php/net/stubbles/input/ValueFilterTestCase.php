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
use net\stubbles\input\error\ParamErrors;
use net\stubbles\lang\types\Date;
use net\stubbles\peer\http\HttpUri;
/**
 * Tests for net\stubbles\input\ValueFilter.
 *
 * @since  1.3.0
 * @group  core
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
        $this->paramErrors = new ParamErrors;
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
    public function asIntReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals(303, $this->createValueFilter(null)->asInt(null, null, 303));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asInt(null, null, 303, true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asInt(null, null, 303, true);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter(4)->asInt(5));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asIntAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter(4)->asInt(5);
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
    public function asFloatReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals(3.03, $this->createValueFilter(null)->asFloat(null, null, 3.03));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asFloat(null, null, 3.03, true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asFloat(null, null, 3.03, true);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter(2.5)->asText(5));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asFloatAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter(2.5)->asText(5);
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
        $this->assertEquals(313, $this->createValueFilter('3.13')->asFloat(null, null, null, false, 2));

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('baz', $this->createValueFilter(null)->asString(null, null, 'baz'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asString(null, null, 'baz', true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asString(null, null, 'baz', true);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asString(5));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asStringAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asString(5);
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
    public function asTextReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $this->assertEquals('baz', $this->createValueFilter(null)->asText(null, null, 'baz'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asText(null, null, 'baz', true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asText(null, null, 'baz', true);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asText(5));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asTextAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asText(5);
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
        $this->assertEquals('foo<b>', $this->createValueFilter('foo<b>')->asText(null, null, null, false, array('b')));

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = array('foo' => 'bar');
        $this->assertEquals($default, $this->createValueFilter(null)->asJson($default));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asJson(array('foo' => 'bar'), true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asJsonAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asJson(array('foo' => 'bar'), true);
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
        $this->assertNull($this->createValueFilter(null)->asPassword());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asPasswordAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asPassword();
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
        $default = array('foo' => 'bar');
        $this->assertEquals('http://example.com/',
                            $this->createValueFilter(null)->asHttpUri(false,
                                                                      HttpUri::fromString('http://example.com/')
                                                            )
                                                          ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asHttpUri(false, null, true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asHttpUriAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asHttpUri(false, null, true);
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
     * @test
     */
    public function asHttpUriReturnsValidValueWithDnsCheckEnabled()
    {
        $this->assertEquals('http://localhost/',
                            $this->createValueFilter('http://localhost/')
                                 ->asHttpUri(true)
                                 ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asMailAddressReturnsNullIfParamIsNullAndNotRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asMailAddress());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asMailAddressReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asMailAddress(true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asMailAddressAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asMailAddress(true);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asMailAddressReturnsNullIfParamIsInvalid()
    {
        $this->assertNull($this->createValueFilter('foo')->asMailAddress());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asMailAddressAddsParamErrorIfParamIsInvalid()
    {
        $this->createValueFilter('foo')->asMailAddress();
        $this->assertTrue($this->paramErrors->existFor('bar'));
    }

    /**
     * @test
     */
    public function asMailAddressReturnsValidValue()
    {
        $this->assertEquals('foo@bar.baz', $this->createValueFilter('foo@bar.baz')->asMailAddress());

    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsDefaultIfParamIsNullAndNotRequired()
    {
        $default = Date::now();
        $this->assertEquals($default, $this->createValueFilter(null)->asDate(null, null, $default));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueFilter(null)->asDate(null, null, Date::now(), true));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function asDateAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueFilter(null)->asDate(null, null, Date::now(), true);
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
    public function ifContainsReturnsValidatedValue()
    {
        $this->assertEquals('303313', $this->createValueFilter('303313')->ifContains('303'));
    }

    /**
     * @test
     */
    public function ifContainsReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueFilter('303313')->ifContains('323'));
    }

    /**
     * @test
     */
    public function ifContainsReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueFilter('303313')->ifContains('323', 'default')
        );
    }

    /**
     * @test
     */
    public function ifContainsReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueFilter(null)->ifContains('323', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsValidatedValue()
    {
        $this->assertEquals('303313', $this->createValueFilter('303313')->ifIsEqualTo('303313'));
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueFilter('303313')->ifIsEqualTo('323313'));
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueFilter('303313')->ifIsEqualTo('323313', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsEqualToReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueFilter(null)->ifIsEqualTo('323313', 'default')
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsValidatedValue()
    {
        $this->assertEquals('http://example.net/',
                            $this->createValueFilter('http://example.net/')->ifIsHttpUri()
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueFilter('invalid')->ifIsHttpUri());
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('http://example.org/',
                            $this->createValueFilter('invalid')->ifIsHttpUri(false,
                                                                             'http://example.org/'
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsHttpUriReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('http://example.org/',
                            $this->createValueFilter(null)->ifIsHttpUri(false,
                                                                        'http://example.org/'
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsValidatedValue()
    {
        $this->assertEquals('127.0.0.1', $this->createValueFilter('127.0.0.1')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueFilter('invalid')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('127.0.0.1',
                            $this->createValueFilter('invalid')->ifIsIpAddress('127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('127.0.0.1',
                            $this->createValueFilter(null)->ifIsIpAddress('127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsValidatedValue()
    {
        $this->assertEquals('example@example.net', $this->createValueFilter('example@example.net')->ifIsMailAddress());
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueFilter('invalid')->ifIsMailAddress());
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('example@example.org',
                            $this->createValueFilter('invalid')->ifIsMailAddress('example@example.org')
        );
    }

    /**
     * @test
     */
    public function ifIsMailAddressReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('example@example.org',
                            $this->createValueFilter(null)->ifIsMailAddress('example@example.org')
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsValidatedValue()
    {
        $this->assertEquals('as value',
                            $this->createValueFilter('as value')->ifIsOneOf(array('as value',
                                                                                  'anothervalue'
                                                                            )
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueFilter('invalid')->ifIsOneOf(array('as value',
                                                                               'anothervalue'
                                                                         )
                                                               )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueFilter('invalid')->ifIsOneOf(array('as value',
                                                                                 'anothervalue'
                                                                           ),
                                                                           'default'
                            )
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueFilter(null)->ifIsOneOf(array('as value',
                                                                                 'anothervalue'
                                                                           ),
                                                                           'default'
                            )
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsValidatedValue()
    {
        $this->assertEquals('a value',
                            $this->createValueFilter('a value')->ifSatisfiesRegex('/^([a-z ])+$/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueFilter('303')->ifSatisfiesRegex('/^([a-z ])+$/'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsDefaultValueIfValidationFails()
    {
        $this->assertEquals('default',
                            $this->createValueFilter('303')->ifSatisfiesRegex('/^([a-z ])+$/',
                                                                              'default'
                            )
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('default',
                            $this->createValueFilter(null)->ifSatisfiesRegex('/^([a-z ])+$/',
                                                                              'default'
                            )
        );
    }

    /**
     * @test
     */
    public function withReturnsValidatedValue()
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(true));
        $this->assertEquals('a value',
                            $this->createValueFilter('a value')->withValidator($mockValidator)
        );
    }

    /**
     * @test
     */
    public function withReturnsNullIfValidatorCanNotValidateValue()
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(false));
        $this->assertNull($this->createValueFilter('a value')->withValidator($mockValidator));
    }

    /**
     * @test
     */
    public function withReturnsDefaultValueIfValidationFails()
    {
        $mockValidator = $this->getMock('net\\stubbles\\input\\validator\\Validator');
        $mockValidator->expects($this->once())
                          ->method('validate')
                          ->with($this->equalTo('a value'))
                          ->will($this->returnValue(false));
        $this->assertEquals('default',
                            $this->createValueFilter('a value')->withValidator($mockValidator,
                                                                                         'default'
                            )
        );
    }

    /**
     * @test
     */
    public function unsecure()
    {
        $this->assertEquals('a value', $this->createValueFilter('a value')->unsecure());
    }

    /**
     * @test
     */
    public function canBeCreatedAsMock()
    {
        $this->assertInstanceOf('net\\stubbles\\input\\ValueFilter',
                                ValueFilter::createAsMock('foo', 'bar')
        );
    }
}
?>