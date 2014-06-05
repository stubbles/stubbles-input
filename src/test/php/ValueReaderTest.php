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
require_once __DIR__ . '/filter/FilterTest.php';
/**
 * Tests for stubbles\input\ValueFilter.
 *
 * @since  1.3.0
 * @group  filter
 */
class ValueReaderTest extends filter\FilterTest
{
    /**
     * @test
     */
    public function ifIsIpAddressReturnsValidatedValue()
    {
        $this->assertEquals('127.0.0.1',
                            $this->createValueReader('127.0.0.1')->ifIsIpAddress()
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('127.0.0.1',
                            $this->createValueReader(null)->ifIsIpAddress('127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsIpAddress('127.0.0.1'));
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFailsAndDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->required()->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressAddsParamErrorIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsIpAddress());
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'INVALID_IP_ADDRESS'));
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->ifIsIpAddress();
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsValidatedValue()
    {
        $this->assertEquals('Hardfloor',
                            $this->createValueReader('Hardfloor')->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('Moby',
                            $this->createValueReader(null)->ifIsOneOf(['Hardfloor', 'Dr DNA'], 'Moby')
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA'], 'Moby'));
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA']));
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFailsAndDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->required()->ifIsOneOf(['Hardfloor', 'Dr DNA']));
    }

    /**
     * @test
     */
    public function ifIsOneOfAddsParamErrorIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA']));
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_NO_SELECT'));
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->ifIsOneOf(['Hardfloor', 'Dr DNA']));
    }

    /**
     * @test
     */
    public function ifIsOneOfAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->ifIsOneOf(['Hardfloor', 'Dr DNA']);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsValidatedValue()
    {
        $this->assertEquals('Hardfloor',
                            $this->createValueReader('Hardfloor')->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsDefaultValueIfParamIsNull()
    {
        $this->assertEquals('Moby',
                            $this->createValueReader(null)->ifSatisfiesRegex('/[a-zA-Z]{9}/', 'Moby')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->ifSatisfiesRegex('/[a-zA-Z]{9}/', 'Moby'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->ifSatisfiesRegex('/[a-zA-Z]{9}/'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFailsAndDefaultValueGiven()
    {
        $this->assertNull($this->createValueReader('invalid')->required()->ifSatisfiesRegex('/[a-zA-Z]{9}/'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexAddsParamErrorIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->ifSatisfiesRegex('/[a-zA-Z]{9}/'));
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_WRONG_VALUE'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfParamIsNullAndRequired()
    {
        $this->assertNull($this->createValueReader(null)->required()->ifSatisfiesRegex('/[a-zA-Z]{9}/'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->createValueReader(null)->required()->ifSatisfiesRegex('/[a-zA-Z]{9}/');
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexAddsParamErrorWithDifferentErrorId()
    {
        $this->createValueReader(null)->required('OTHER')->ifSatisfiesRegex('/[a-zA-Z]{9}/');
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    /**
     * @test
     */
    public function returnsNullIfParamHasErrors()
    {
        $param = new Param('bar', 'foo');
        $param->addError('SOME_ERROR');
        $mockFilter = $this->getMock('stubbles\input\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->with($this->equalTo($param))
                   ->will($this->returnValue('baz'));
        $this->assertNull($this->createValueReaderWithParam($param)->withFilter($mockFilter));
    }

    /**
     * @test
     */
    public function errorListContainsParamError()
    {
        $param = new Param('bar', 'foo');
        $param->addError('SOME_ERROR');
        $mockFilter = $this->getMock('stubbles\input\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->with($this->equalTo($param))
                   ->will($this->returnValue('baz'));
        $this->createValueReaderWithParam($param)->withFilter($mockFilter);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'SOME_ERROR'));
    }

    /**
     * @test
     */
    public function returnsValueFromFilter()
    {
        $mockFilter = $this->getMock('stubbles\input\Filter');
        $mockFilter->expects($this->once())
                   ->method('apply')
                   ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->createValueReader('foo')->withFilter($mockFilter));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canChangeRequiredParamErrorId()
    {
        $this->createValueReader(null)->required('OTHER')->withFilter($this->getMock('stubbles\input\Filter'));
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    /**
     * @test
     */
    public function unsecureReturnsRawValue()
    {
        $this->assertEquals('a value', $this->createValueReader('a value')->unsecure());
    }

    /**
     * @test
     */
    public function canBeCreatedWithoutParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                ValueReader::forValue('bar')
        );
    }

    /**
     * @test
     */
    public function canBeCreatedforParam()
    {
        $this->assertInstanceOf('stubbles\input\ValueReader',
                                ValueReader::forParam(new Param('foo', 'bar'))
        );
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withFunctionReturnsFilteredValue()
    {
        $this->assertEquals('Roland TB-303',
                            $this->createValueReader('303')
                                 ->withFunction(function(Param $param)
                                                {
                                                    if ($param->value() == 303) {
                                                        return 'Roland TB-303';
                                                    }

                                                    $param->addErrorWithId('INVALID_303');
                                                    return null;
                                                }
                                   )
        );
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withFunctionReturnsNullOnError()
    {
        $this->assertNull($this->createValueReader('909')
                               ->withFunction(function(Param $param)
                                              {
                                                  if ($param->value() == 303) {
                                                      return 'Roland TB-303';
                                                  }

                                                  $param->addError('INVALID_303');
                                                  return null;
                                              }
                                   )
        );
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withFunctionAddsErrorsToErrorList()
    {
        $this->createValueReader('909')
             ->withFunction(function(Param $param)
                            {
                                if ($param->value() == 303) {
                                    return 'Roland TB-303';
                                }

                                $param->addError('INVALID_303');
                                return null;
                            }
        );
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'INVALID_303'));
    }
}
