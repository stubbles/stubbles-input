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
                            $this->createValueReader(null)->defaultingTo('127.0.0.1')->ifIsIpAddress()
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->defaultingTo('127.0.0.1')->ifIsIpAddress());
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
                            $this->createValueReader(null)->defaultingTo('Moby')->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->defaultingTo('Moby')->ifIsOneOf(['Hardfloor', 'Dr DNA']));
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
                            $this->createValueReader(null)->defaultingTo('Moby')->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFails()
    {
        $this->assertNull($this->createValueReader('invalid')->defaultingTo('Moby')->ifSatisfiesRegex('/[a-zA-Z]{9}/'));
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
     * @since  3.0.0
     */
    public function withFilterReturnsNullIfParameterNotSet()
    {
        $param = new Param('bar', null);
        $mockFilter = $this->getMock('stubbles\input\Filter');
        $mockFilter->expects($this->never())
                   ->method('apply');
        $this->assertNull($this->createValueReaderWithParam($param)->withFilter($mockFilter));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function withFilterReturnsDefaultValueIfParameterNotSet()
    {
        $param = new Param('bar', null);
        $mockFilter = $this->getMock('stubbles\input\Filter');
        $mockFilter->expects($this->never())
                   ->method('apply');
        $this->assertEquals(
                'foo',
                $this->createValueReaderWithParam($param)->defaultingTo('foo')->withFilter($mockFilter)
        );
    }

    /**
     * @test
     */
    public function withFilterReturnsNullIfParamHasErrors()
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
    public function withFilterErrorListContainsParamError()
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
    public function withFilterReturnsNullIfParamRequiredButNotSet()
    {
        $param = new Param('bar', null);
        $mockFilter = $this->getMock('stubbles\input\Filter');
        $mockFilter->expects($this->never())
                   ->method('apply');
        $this->assertNull($this->createValueReaderWithParam($param)->required()->withFilter($mockFilter));
    }

    /**
     * @test
     */
    public function withFilterAddsRequiredErrorWhenRequiredAndParamNotSet()
    {
        $param = new Param('bar', null);
        $mockFilter = $this->getMock('stubbles\input\Filter');
        $mockFilter->expects($this->never())
                   ->method('apply');
        $this->createValueReaderWithParam($param)->required()->withFilter($mockFilter);
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function withFilterReturnsValueFromFilter()
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
     * create a simple callable which filters a param value
     *
     * @return  \Closure
     */
    private function createCallable()
    {
        return function(Param $param)
               {
                   if ($param->value() == 303) {
                       return 'Roland TB-303';
                   }

                   $param->addError('INVALID_303');
                   return null;
               };
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withCallableReturnsFilteredValue()
    {
        $this->assertEquals(
                'Roland TB-303',
                $this->createValueReader('303')->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableReturnsNullWhenParamNotSet()
    {
        $this->assertNull(
                $this->createValueReader(null)->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableReturnsNullWhenParamNotSetAndRequired()
    {
        $this->assertNull(
                $this->createValueReader(null)->required()->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableAddsErrorWhenParamNotSetAndRequired()
    {
        $this->createValueReader(null)->required()->withCallable($this->createCallable());
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableReturnsDefaultValueWhenParamNotSet()
    {
        $this->assertEquals(
                'Roland TB-303 w/ Hardfloor Mod',
                $this->createValueReader(null)->defaultingTo('Roland TB-303 w/ Hardfloor Mod')->withCallable($this->createCallable())
        );
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withCallableReturnsNullOnError()
    {
        $this->assertNull(
                $this->createValueReader('909')->withCallable($this->createCallable())
        );
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withCallableAddsErrorsToErrorList()
    {
        $this->createValueReader('909')->withCallable($this->createCallable());
        $this->assertTrue($this->paramErrors->existForWithId('bar', 'INVALID_303'));
    }
}
