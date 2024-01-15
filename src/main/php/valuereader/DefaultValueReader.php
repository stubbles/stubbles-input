<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\valuereader;

use BadMethodCallException;
use Closure;
use LogicException;
use stdClass;
use stubbles\date\Date;
use stubbles\date\span\Datespan;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\date\span\Week;
use stubbles\input\Filter;
use stubbles\input\filter\JsonFilter;
use stubbles\input\filter\PasswordChecker;
use stubbles\input\filter\range\DateRange;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\filter\range\SecretMinLength;
use stubbles\input\filter\range\StringLength;
use stubbles\input\filter\range\NumberRange;
use stubbles\peer\http\HttpUri;
use stubbles\values\Secret;

use function stubbles\values\typeOf;
/**
 * Represents a default value if actual value is not present.
 *
 * @since  3.0.0
 */
class DefaultValueReader implements CommonValueReader
{
    /**
     * @param  mixed  $default  default value to return if value is not present
     */
    public function __construct(private mixed $default) { }

    /**
     * checks type of default value
     *
     * @param   Closure  $isCorrectType  check to be executed when default value is not null
     * @param   string   $expectedType   expected type of default value
     * @throws  LogicException
     */
    private function checkDefaultType(Closure $isCorrectType, string $expectedType): void
    {
        if (!$isCorrectType()) {
            throw new LogicException(
                'Default value is not of type ' . $expectedType
                . ' but of type ' . typeOf($this->default)
            );
        }
    }

    /**
     * read as array value
     *
     * In case the default value is not of type array an IllegalStateException
     * will be thrown.
     *
     * @param   string  $separator  optional  character to split input value with
     * @return  mixed[]
     */
    public function asArray(string $separator = self::ARRAY_SEPARATOR): ?array
    {
        $this->checkDefaultType(fn(): bool => is_array($this->default), 'array');
        return $this->default;
    }

    /**
     * read as boolean value
     *
     * Will cast any default value to bool.
     */
    public function asBool(): ?bool
    {
        return (bool) $this->default;
    }

    /**
     * read as integer value
     *
     * In case the default value is not of type int an LogicException
     * will be thrown.
     */
    public function asInt(NumberRange $range = null): ?int
    {
        $this->checkDefaultType(fn(): bool => is_int($this->default), 'int');
        return $this->default;
    }

    /**
     * read as float value
     *
     * In case the default value is not of type float an LogicException
     * will be thrown.
     */
    public function asFloat(NumberRange $range = null, int $decimals = null): ?float
    {
        $this->checkDefaultType(fn(): bool => is_float($this->default), 'float');
        return $this->default;
    }

    /**
     * read as string value
     */
    public function asString(StringLength $length = null): ?string
    {
        return $this->default;
    }

    /**
     * read as string value
     */
    public function asSecret(SecretMinLength $length = null): ?Secret
    {
        $this->checkDefaultType(fn(): bool => $this->default instanceof Secret, Secret::class);
        return $this->default;
    }

    /**
     * read as text value
     *
     * @param   string[]  $allowedTags  list of allowed tags
     */
    public function asText(StringLength $length = null, array $allowedTags = []): ?string
    {
        return $this->default;
    }

    /**
     * read as json value
     *
     * @param   int  $maxLength  maximum allowed length of incoming JSON document in bytes  optional
     * @return  stdClass|array<mixed>|null
     */
    public function asJson(int $maxLength = JsonFilter::DEFAULT_MAX_LENGTH): stdClass|array|null
    {
        return $this->default;
    }

    /**
     * read as password value
     *
     * Default values for passwords make no sense, therefor all calls to this
     * method trigger a BadMethodCallException.
     *
     * @throws  BadMethodCallException
     */
    public function asPassword(PasswordChecker $checker): ?Secret
    {
        throw new BadMethodCallException('Default passwords are not supported');
    }

    /**
     * read as http uri
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * LogicException will be thrown.
     */
    public function asHttpUri(): ?HttpUri
    {
        $this->checkDefaultType(fn(): bool => $this->default instanceof HttpUri, HttpUri::class);
        return $this->default;
    }

    /**
     * read as http uri if it does exist
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * LogicException will be thrown.
     */
    public function asExistingHttpUri(): ?HttpUri
    {
        return $this->asHttpUri();
    }

    /**
     * returns value if it is a mail address, and null otherwise
     */
    public function asMailAddress(): ?string
    {
        return $this->default;
    }

    /**
     * read as date value
     */
    public function asDate(DateRange $range = null): ?Date
    {
        $this->default = null === $this->default ? null : Date::castFrom($this->default, 'default');
        return $this->default;
    }

    /**
     * read as day
     *
     * In case the default value is not of type stubbles\date\span\Day an
     * LogicException will be thrown.
     */
    public function asDay(DatespanRange $range = null): ?Day
    {
        $this->checkDefaultType(fn(): bool => $this->default instanceof Day, Day::class);
        return $this->default;
    }

    /**
     * read as week
     *
     * In case the default value is not of type stubbles\date\span\Week an
     * LogicException will be thrown.
     *
     * @since   4.5.0
     */
    public function asWeek(DatespanRange $range = null): ?Week
    {
        $this->checkDefaultType(fn(): bool => $this->default instanceof Week, Week::class);
        return $this->default;
    }

    /**
     * read as month
     *
     * In case the default value is not of type stubbles\date\span\Month an
     * LogicException will be thrown.
     */
    public function asMonth(DatespanRange $range = null): ?Month
    {
        $this->checkDefaultType(fn(): bool => $this->default instanceof Month, Month::class);
        return $this->default;
    }

    /**
     * read as datespan
     *
     * In case the default value is not of type stubbles\date\span\Datespan an
     * LogicException will be thrown.
     *
     * @since  4.3.0
     */
    public function asDatespan(DatespanRange $range = null): ?Datespan
    {
        $this->checkDefaultType(fn() => $this->default instanceof Datespan, Datespan::class);
        return $this->default;
    }

    /**
     * returns value if it is an ip address, and null otherwise
     */
    public function ifIsIpAddress(): ?string
    {
        return $this->default;
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param  string[]  $allowedValues  list of allowed values
     */
    public function ifIsOneOf(array $allowedValues): ?string
    {
        return $this->default;
    }

    /**
     * returns value if it is matched by given regular expression
     *
     * @since  6.0.0
     */
    public function ifMatches(string $regex): ?string
    {
        return $this->default;
    }

    /**
     * returns param value when given predicate evaluates to true
     *
     * If value does not satisfy the predicate return value will be null.
     *
     * @api
     * @param  string                $errorId    error id to be used in case validation fails
     * @param  array<string,scalar>  $details    optional  details for param error in case validation fails
     * @since  3.0.0
     */
    public function when(callable $predicate, string $errorId, array $details = []): ?string
    {
        return $this->default;
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * If it is required but value is null an error will be added to the list
     * of param errors.
     */
    public function withFilter(Filter $filter): mixed
    {
        return $this->default;
    }

    /**
     * checks value with given callable
     *
     * The callable must accept an instance of stubbles\input\Param and
     * return the filtered value.
     * <code>
     * $result = $request->readParam('name')
     *     ->withCallable(
     *         function(Param $param) {
     *             if ($param->getValue() == 303) {
     *                 return 'Roland TB-303';
     *             }
     *
     *             $param->addErrorWithId('INVALID_303');
     *             return null;
     *          }
     *     );
     * </code>
     */
    public function withCallable(callable $filter): mixed
    {
        return $this->default;
    }
}
