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
use bovigo\callmap\NewInstance;
use stubbles\input\errors\ParamError;
use stubbles\values\Value;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\callmap\verify;

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
        assert(
                $this->readParam('127.0.0.1')->ifIsIpAddress(),
                equals('127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function ifIsIpAddressReturnsDefaultValueIfParamIsNull()
    {
        assert(
                $this->readParam(null)->defaultingTo('127.0.0.1')->ifIsIpAddress(),
                equals('127.0.0.1')
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
        assertNull($this->readParam('invalid')->required()->ifIsIpAddress());
    }

    /**
     * @test
     */
    public function ifIsIpAddressAddsParamErrorIfValidationFails()
    {
        $this->readParam('invalid')->ifIsIpAddress();
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
        assert(
                $this->readParam('Hardfloor')->ifIsOneOf(['Hardfloor', 'Dr DNA']),
                equals('Hardfloor')
        );
    }

    /**
     * @test
     */
    public function ifIsOneOfReturnsDefaultValueIfParamIsNull()
    {
        assert(
                $this->readParam(null)
                        ->defaultingTo('Moby')
                        ->ifIsOneOf(['Hardfloor', 'Dr DNA']),
                equals('Moby')
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
        $this->readParam('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA']);
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
    public function ifMatchesReturnsValidatedValue()
    {
        assert(
                $this->readParam('Hardfloor')->ifMatches('/[a-zA-Z]{9}/'),
                equals('Hardfloor')
        );
    }

    /**
     * @test
     */
    public function ifMatchesReturnsDefaultValueIfParamIsNull()
    {
        assert(
                $this->readParam(null)
                        ->defaultingTo('Moby')
                        ->ifMatches('/[a-zA-Z]{9}/'),
                equals('Moby')
        );
    }

    /**
     * @test
     */
    public function ifMatchesReturnsNullIfValidationFails()
    {
        assertNull(
                $this->readParam('invalid')
                        ->defaultingTo('Moby')
                        ->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifMatchesReturnsNullIfValidationFailsAndNoDefaultValueGiven()
    {
        assertNull(
                $this->readParam('invalid')->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifMatchesReturnsNullIfValidationFailsAndDefaultValueGiven()
    {
        assertNull(
                $this->readParam('invalid')
                        ->required()
                        ->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifMatchesAddsParamErrorIfValidationFails()
    {
        $this->readParam('invalid')->ifMatches('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_WRONG_VALUE'));
    }

    /**
     * @test
     */
    public function ifMatchesReturnsNullIfParamIsNullAndRequired()
    {
        assertNull(
                $this->readParam(null)->required()->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    /**
     * @test
     */
    public function ifMatchesAddsParamErrorIfParamIsNullAndRequired()
    {
        $this->readParam(null)->required()->ifMatches('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @test
     */
    public function ifMatchesAddsParamErrorWithDifferentErrorId()
    {
        $this->readParam(null)->required('OTHER')->ifMatches('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function withFilterReturnsNullIfParameterNotSet()
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        assertNull($this->read($value)->withFilter($filter));
        verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function withFilterReturnsDefaultValueIfParameterNotSet()
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        assert(
                $this->read($value)->defaultingTo('foo')->withFilter($filter),
                equals('foo')
        );
        verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function withFilterReturnsNullIfParamHasErrors()
    {
        $filter = NewInstance::of(Filter::class)->mapCalls([
                'apply' => [null, ['SOME_ERROR' => new ParamError('SOME_ERROR')]]
        ]);
        assertNull($this->read(Value::of('foo'))->withFilter($filter));
    }

    /**
     * @test
     */
    public function withFilterErrorListContainsParamError()
    {
        $value = Value::of('foo');
        $filter = NewInstance::of(Filter::class)->mapCalls([
                'apply' => [null, ['SOME_ERROR' => new ParamError('SOME_ERROR')]]
        ]);
        $this->read($value)->withFilter($filter);
        assertTrue($this->paramErrors->existForWithId('bar', 'SOME_ERROR'));
    }

    /**
     * @test
     */
    public function withFilterReturnsNullIfParamRequiredButNotSet()
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        assertNull($this->read($value)->required()->withFilter($filter));
        verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function withFilterAddsRequiredErrorWhenRequiredAndParamNotSet()
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        $this->read($value)->required()->withFilter($filter);
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
        verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function withFilterReturnsValueFromFilter()
    {
        $filter = NewInstance::of(Filter::class)->mapCalls(['apply' => ['foo', []]]);
        assert($this->readParam('foo')->withFilter($filter), equals('foo'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canChangeRequiredParamErrorId()
    {
        $this->readParam(null)
                ->required('OTHER')
                ->withFilter(NewInstance::of(Filter::class));
        assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    /**
     * @test
     */
    public function unsecureReturnsRawValue()
    {
        assert($this->readParam('a value')->unsecure(), equals('a value'));
    }

    /**
     * @test
     */
    public function canBeCreatedWithoutParam()
    {
        assert(ValueReader::forValue('bar'), isInstanceOf(ValueReader::class));
    }

    /**
     * @test
     */
    public function canBeCreatedforParam()
    {
        assert(
                ValueReader::forParam(new Param('foo', 'bar')),
                isInstanceOf(ValueReader::class)
        );
    }

    /**
     * create a simple callable which filters a param value
     *
     * @return  \Closure
     */
    private function createCallable()
    {
        return function(Value $value, array &$errors)
               {
                   if ($value->value() == 303) {
                       return 'Roland TB-303';
                   }

                   $errors['INVALID_303'] = [];
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
        assert(
                $this->readParam('303')->withCallable($this->createCallable()),
                equals('Roland TB-303')
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
        assert(
                $this->readParam(null)
                        ->defaultingTo('Roland TB-303 w/ Hardfloor Mod')
                        ->withCallable($this->createCallable()),
                equals('Roland TB-303 w/ Hardfloor Mod')
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
