<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\input\errors\ParamError;
use stubbles\input\filter\FilterTestBase;
use stubbles\values\Value;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\callmap\verify;

/**
 * Tests for stubbles\input\ValueFilter.
 *
 * @since  1.3.0
 */
#[Group('filter')]
class ValueReaderTest extends FilterTestBase
{
    private const LOCALHOST_IP = '127.0.0.1';

    #[Test]
    public function ifIsIpAddressReturnsValidatedValue(): void
    {
        assertThat(
            $this->readParam(self::LOCALHOST_IP)->ifIsIpAddress(),
            equals(self::LOCALHOST_IP)
        );
    }

    #[Test]
    public function ifIsIpAddressReturnsDefaultValueIfParamIsNull(): void
    {
        assertThat(
            $this->readParam(null)->defaultingTo(self::LOCALHOST_IP)->ifIsIpAddress(),
            equals(self::LOCALHOST_IP)
        );
    }

    #[Test]
    public function ifIsIpAddressReturnsNullIfValidationFails(): void
    {
        assertNull(
            $this->readParam('invalid')
                ->defaultingTo(self::LOCALHOST_IP)
                ->ifIsIpAddress()
        );
    }

    #[Test]
    public function ifIsIpAddressReturnsNullIfValidationFailsAndNoDefaultValueGiven(): void
    {
        assertNull($this->readParam('invalid')->ifIsIpAddress());
    }

    #[Test]
    public function ifIsIpAddressReturnsNullIfValidationFailsAndDefaultValueGiven(): void
    {
        assertNull($this->readParam('invalid')->required()->ifIsIpAddress());
    }

    #[Test]
    public function ifIsIpAddressAddsParamErrorIfValidationFails(): void
    {
        $this->readParam('invalid')->ifIsIpAddress();
        assertTrue($this->paramErrors->existForWithId('bar', 'INVALID_IP_ADDRESS'));
    }

    #[Test]
    public function ifIsIpAddressReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull($this->readParam(null)->required()->ifIsIpAddress());
    }

    #[Test]
    public function ifIsIpAddressAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->ifIsIpAddress();
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    #[Test]
    public function ifIsOneOfReturnsValidatedValue(): void
    {
        assertThat(
            $this->readParam('Hardfloor')->ifIsOneOf(['Hardfloor', 'Dr DNA']),
            equals('Hardfloor')
        );
    }

    #[Test]
    public function ifIsOneOfReturnsDefaultValueIfParamIsNull(): void
    {
        assertThat(
            $this->readParam(null)
                ->defaultingTo('Moby')
                ->ifIsOneOf(['Hardfloor', 'Dr DNA']),
            equals('Moby')
        );
    }

    #[Test]
    public function ifIsOneOfReturnsNullIfValidationFails(): void
    {
        assertNull(
            $this->readParam('invalid')
                ->defaultingTo('Moby')
                ->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    #[Test]
    public function ifIsOneOfReturnsNullIfValidationFailsAndNoDefaultValueGiven(): void
    {
        assertNull(
            $this->readParam('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    #[Test]
    public function ifIsOneOfReturnsNullIfValidationFailsAndDefaultValueGiven(): void
    {
        assertNull(
            $this->readParam('invalid')
                ->required()
                ->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    #[Test]
    public function ifIsOneOfAddsParamErrorIfValidationFails(): void
    {
        $this->readParam('invalid')->ifIsOneOf(['Hardfloor', 'Dr DNA']);
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_NO_SELECT'));
    }

    #[Test]
    public function ifIsOneOfReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull(
            $this->readParam(null)
                ->required()
                ->ifIsOneOf(['Hardfloor', 'Dr DNA'])
        );
    }

    #[Test]
    public function ifIsOneOfAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)
            ->required()
            ->ifIsOneOf(['Hardfloor', 'Dr DNA']);

        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    #[Test]
    public function ifMatchesReturnsValidatedValue(): void
    {
        assertThat(
            $this->readParam('Hardfloor')->ifMatches('/[a-zA-Z]{9}/'),
            equals('Hardfloor')
        );
    }

    #[Test]
    public function ifMatchesReturnsDefaultValueIfParamIsNull(): void
    {
        assertThat(
            $this->readParam(null)
                ->defaultingTo('Moby')
                ->ifMatches('/[a-zA-Z]{9}/'),
            equals('Moby')
        );
    }

    #[Test]
    public function ifMatchesReturnsNullIfValidationFails(): void
    {
        assertNull(
            $this->readParam('invalid')
                ->defaultingTo('Moby')
                ->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    #[Test]
    public function ifMatchesReturnsNullIfValidationFailsAndNoDefaultValueGiven(): void
    {
        assertNull(
            $this->readParam('invalid')->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    #[Test]
    public function ifMatchesReturnsNullIfValidationFailsAndDefaultValueGiven(): void
    {
        assertNull(
            $this->readParam('invalid')
                ->required()
                ->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    #[Test]
    public function ifMatchesAddsParamErrorIfValidationFails(): void
    {
        $this->readParam('invalid')->ifMatches('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_WRONG_VALUE'));
    }

    #[Test]
    public function ifMatchesReturnsNullIfParamIsNullAndRequired(): void
    {
        assertNull(
            $this->readParam(null)->required()->ifMatches('/[a-zA-Z]{9}/')
        );
    }

    #[Test]
    public function ifMatchesAddsParamErrorIfParamIsNullAndRequired(): void
    {
        $this->readParam(null)->required()->ifMatches('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    #[Test]
    public function ifMatchesAddsParamErrorWithDifferentErrorId(): void
    {
        $this->readParam(null)->required('OTHER')->ifMatches('/[a-zA-Z]{9}/');
        assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function withFilterReturnsNullIfParameterNotSet(): void
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        assertNull($this->read($value)->withFilter($filter));
        verify($filter, 'apply')->wasNeverCalled();
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function withFilterReturnsDefaultValueIfParameterNotSet(): void
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        assertThat(
            $this->read($value)->defaultingTo('foo')->withFilter($filter),
            equals('foo')
        );
        verify($filter, 'apply')->wasNeverCalled();
    }

    #[Test]
    public function withFilterReturnsNullIfParamHasErrors(): void
    {
        $filter = NewInstance::of(Filter::class)->returns([
            'apply' => [null, ['SOME_ERROR' => new ParamError('SOME_ERROR')]]
        ]);
        assertNull($this->read(Value::of('foo'))->withFilter($filter));
    }

    #[Test]
    public function withFilterErrorListContainsParamError(): void
    {
        $value = Value::of('foo');
        $filter = NewInstance::of(Filter::class)->returns([
            'apply' => [null, ['SOME_ERROR' => new ParamError('SOME_ERROR')]]
        ]);
        $this->read($value)->withFilter($filter);
        assertTrue($this->paramErrors->existForWithId('bar', 'SOME_ERROR'));
    }

    #[Test]
    public function withFilterReturnsNullIfParamRequiredButNotSet(): void
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        assertNull($this->read($value)->required()->withFilter($filter));
        verify($filter, 'apply')->wasNeverCalled();
    }

    #[Test]
    public function withFilterAddsRequiredErrorWhenRequiredAndParamNotSet(): void
    {
        $value  = Value::of(null);
        $filter = NewInstance::of(Filter::class);
        $this->read($value)->required()->withFilter($filter);
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
        verify($filter, 'apply')->wasNeverCalled();
    }

    #[Test]
    public function withFilterReturnsValueFromFilter(): void
    {
        $filter = NewInstance::of(Filter::class)->returns(['apply' => ['foo', []]]);
        assertThat($this->readParam('foo')->withFilter($filter), equals('foo'));
    }

    /**
     * @since  2.0.0
     */
    #[Test]
    public function canChangeRequiredParamErrorId(): void
    {
        $this->readParam(null)
            ->required('OTHER')
            ->withFilter(NewInstance::of(Filter::class));
        assertTrue($this->paramErrors->existForWithId('bar', 'OTHER'));
    }

    #[Test]
    public function unsecureReturnsRawValue(): void
    {
        assertThat($this->readParam('a value')->unsecure(), equals('a value'));
    }

    #[Test]
    public function canBeCreatedWithoutParam(): void
    {
        assertThat(ValueReader::forValue('bar'), isInstanceOf(ValueReader::class));
    }

    private function createCallable(): callable
    {
        return function(Value $value, array &$errors): ?string
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
     */
    #[Test]
    #[Group('issue_33')]
    public function withCallableReturnsFilteredValue(): void
    {
        assertThat(
                $this->readParam('303')->withCallable($this->createCallable()),
                equals('Roland TB-303')
        );
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function withCallableReturnsNullWhenParamNotSet(): void
    {
        assertNull(
            $this->readParam(null)->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function withCallableReturnsNullWhenParamNotSetAndRequired(): void
    {
        assertNull(
            $this->readParam(null)->required()->withCallable($this->createCallable())
        );
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function withCallableAddsErrorWhenParamNotSetAndRequired(): void
    {
        $this->readParam(null)->required()->withCallable($this->createCallable());
        assertTrue($this->paramErrors->existForWithId('bar', 'FIELD_EMPTY'));
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function withCallableReturnsDefaultValueWhenParamNotSet(): void
    {
        assertThat(
            $this->readParam(null)
                ->defaultingTo('Roland TB-303 w/ Hardfloor Mod')
                ->withCallable($this->createCallable()),
            equals('Roland TB-303 w/ Hardfloor Mod')
        );
    }

    /**
     * @since  2.2.0
     */
    #[Test]
    #[Group('issue_33')]
    public function withCallableReturnsNullOnError(): void
    {
        assertNull(
            $this->readParam('909')->withCallable($this->createCallable())
        );
    }

    /**
     * @since  2.2.0
     */
    #[Test]
    #[Group('issue_33')]
    public function withCallableAddsErrorsToErrorList(): void
    {
        $this->readParam('909')->withCallable($this->createCallable());
        assertTrue($this->paramErrors->existForWithId('bar', 'INVALID_303'));
    }
}
