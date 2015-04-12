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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
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
        assertEquals(
                '127.0.0.1',
                $this->readParam('127.0.0.1')->ifIsIpAddress()
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfParamIsNull()
    {
        assertEquals(
                '127.0.0.1',
                $this->readParam(null)->defaultingTo('127.0.0.1')->ifIsIpAddress()
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFails()
    {
        assertNull(
                $this->readParam('invalid')
                        ->defaultingTo('127.0.0.1')
                        ->ifIsIpAddress()
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        assertNull($this->readParam('invalid')->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfValidationFailsAndDefaultValueGiven()
    {
        assertNull(
                $this->readParam('invalid')->required()->ifIsIpAddress()
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressAddsParamErrorIfValidationFails()
    {
        assertNull($this->readParam('invalid')->ifIsIpAddress());
        assertTrue($this->paramErrors->existForWithId('bar', 'INVALID_IP_ADDRESS'));
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsNullIfParamIsNullAndRequired()
    {
        assertNull($this->readParam(null)->required()->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->ifIsIpAddress();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsValidatedValue()
    {
        assertEquals(
                'Hardfloor',
                $this->readParam('Hardfloor')->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfParamIsNull()
    {
        assertEquals(
                'Moby',
                $this->readParam(null)
                        ->defaultingTo('Moby')
                        ->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFails()
    {
        assertNull(
                $this->readParam('invalid')
                        ->defaultingTo('Moby')
                        ->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        assertNull(
                $this->readParam('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfValidationFailsAndDefaultValueGiven()
    {
        assertNull(
                $this->readParam('invalid')
                        ->required()
                        ->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfAddsParamErrorIfValidationFails()
    {
        assertNull(
                $this->readParam('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_NO_SELECT'));
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsNullIfParamIsNullAndRequired()
    {
        assertNull(
                $this->readParam(null)
                        ->required()
                        ->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)
                ->required()
                ->ifIsOneOf(['Hardfloor', 'Dr DNA']);
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsValidatedValue()
    {
        assertEquals(
                'Hardfloor',
                $this->readParam('Hardfloor')->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsDefaultValueIfParamIsNull()
    {
        assertEquals(
                'Moby',
                $this->readParam(null)
                        ->defaultingTo('Moby')
                        ->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFails()
    {
        assertNull(
                $this->readParam('invalid')
                        ->defaultingTo('Moby')
                        ->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        assertNull(
                $this->readParam('invalid')->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfValidationFailsAndDefaultValueGiven()
    {
        assertNull(
                $this->readParam('invalid')
                        ->required()
                        ->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexAddsParamErrorIfValidationFails()
    {
        assertNull(
                $this->readParam('invalid')->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_WRONG_VALUE'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexReturnsNullIfParamIsNullAndRequired()
    {
        assertNull(
                $this->readParam(null)->required()->ifSatisfiesRegex('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->ifSatisfiesRegex('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function ifSatisfiesRegexAddsParamErrorWithDifferentErrorId()
    {
        $this->readParam(null)->required('OTHER')->ifSatisfiesRegex('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function withFilterReturnsNullIfParameterNotSet()
    {
        $param  = new Param('bar', null);
        $filter = NewInstance::of('stubbles\input\Filter');
        assertNull($this->read($param)->withFilter($filter));
        callmap\verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function withFilterReturnsDefaultValueIfParameterNotSet()
    {
        $param  = new Param('bar', null);
        $filter = NewInstance::of('stubbles\input\Filter');
        assertEquals(
                'foo',
                $this->read($param)->defaultingTo('foo')->withFilter($filter)
        );
        callmap\verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function withFilterReturnsNullIfParamHasErrors()
    {
        $param = new Param('bar', 'foo');
        $param->addError('SOME_ERROR');
        $filter = NewInstance::of('stubbles\input\Filter')
                ->mapCalls(['apply' => 'baz']);
        assertNull($this->read($param)->withFilter($filter));
    }

    /**
     * @test
     */
    public function withFilterErrorListContainsParamError()
    {
        $param = new Param('bar', 'foo');
        $param->addError('SOME_ERROR');
        $filter = NewInstance::of('stubbles\input\Filter')
                ->mapCalls(['apply' => 'baz']);
        $this->read($param)->withFilter($filter);
        assertTrue($this->paramErrors->existForWithId('bar', 'SOME_ERROR'));
    }

    /**
     * @test
     */
    public function withFilterReturnsNullIfParamRequiredButNotSet()
    {
        $param  = new Param('bar', null);
        $filter = NewInstance::of('stubbles\input\Filter');
        assertNull($this->read($param)->required()->withFilter($filter));
        callmap\verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function withFilterAddsRequiredErrorWhenRequiredAndParamNotSet()
    {
        $param = new Param('bar', null);
        $filter = NewInstance::of('stubbles\input\Filter');
        $this->read($param)->required()->withFilter($filter);
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
        callmap\verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function withFilterReturnsValueFromFilter()
    {
        $filter = NewInstance::of('stubbles\input\Filter')
                ->mapCalls(['apply' => 'foo']);
        assertEquals('foo', $this->readParam('foo')->withFilter($filter));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canChangeRequiredParamErrorId()
    {
        $this->readParam(null)
                ->required('OTHER')
                ->withFilter(NewInstance::of('stubbles\input\Filter'));
        assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    /**
     * @test
     */
    public function unsecureReturnsRawValue()
    {
        assertEquals('a value', $this->readParam('a value')->unsecure());
    }

    /**
     * @test
     */
    public function canBeCreatedWithoutParam()
    {
        assertInstanceOf(
                'stubbles\input\ValueReader',
                ValueReader::forValue('bar')
        );
    }

    /**
     * @test
     */
    public function canBeCreatedforParam()
    {
        assertInstanceOf(
                'stubbles\input\ValueReader',
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
        assertEquals(
                'Roland TB-303',
                $this->readParam('303')->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableReturnsNullWhenParamNotSet()
    {
        assertNull(
                $this->readParam(null)->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableReturnsNullWhenParamNotSetAndRequired()
    {
        assertNull(
                $this->readParam(null)->required()->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableAddsErrorWhenParamNotSetAndRequired()
    {
        $this->readParam(null)->required()->withCallable($this->createCallable());
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  3.0.0
     * @test
     */
    public function withCallableReturnsDefaultValueWhenParamNotSet()
    {
        assertEquals(
                'Roland TB-303 w/ Hardfloor Mod',
                $this->readParam(null)
                        ->defaultingTo('Roland TB-303 w/ Hardfloor Mod')
                        ->withCallable($this->createCallable())
        );
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withCallableReturnsNullOnError()
    {
        assertNull(
                $this->readParam('909')->withCallable($this->createCallable())
        );
    }

    /**
     * @since  2.2.0
     * @group  issue_33
     * @test
     */
    public function withCallableAddsErrorsToErrorList()
    {
        $this->readParam('909')->withCallable($this->createCallable());
        assertTrue($this->paramErrors->existForWithId('bar', 'INVALID_303'));
    }
}
