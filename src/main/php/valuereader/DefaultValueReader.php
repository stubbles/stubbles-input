<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\valuereader;
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
     * a default value to return if value is not present
     *
     * @type  mixed
     */
    private $default;

    /**
     * constructor
     *
     * @param  mixed  $default
     */
    public function __construct($default)
    {
        $this->default = $default;
    }

    /**
     * checks type of default value
     *
     * @param   \Closure  $isCorrectType         check to be executed when default value is not null
     * @param   string    $expectedType  expected type of default value
     * @throws  \LogicException
     */
    private function checkDefaultType(\Closure $isCorrectType, string $expectedType)
    {
        if (!$isCorrectType()) {
            throw new \LogicException(
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
     * @return  array
     */
    public function asArray(string $separator = self::ARRAY_SEPARATOR): ?array
    {
        $this->checkDefaultType(function() { return is_array($this->default);}, 'array');
        return $this->default;
    }

    /**
     * read as boolean value
     *
     * Will cast any default value to bool.
     *
     * @return  bool
     */
    public function asBool(): ?bool
    {
        return (bool) $this->default;
    }

    /**
     * read as integer value
     *
     * In case the default value is not of type int an IllegalStateException
     * will be thrown.
     *
     * @param   \stubbles\input\filter\range\NumberRange  $range
     * @return  int
     */
    public function asInt(NumberRange $range = null): ?int
    {
        $this->checkDefaultType(function() { return is_int($this->default);}, 'int');
        return $this->default;
    }

    /**
     * read as float value
     *
     * In case the default value is not of type float an IllegalStateException
     * will be thrown.
     *
     * @param   \stubbles\input\filter\range\NumberRange  $range
     * @param   int                                       $decimals  number of decimals
     * @return  float
     */
    public function asFloat(NumberRange $range = null, int $decimals = null): ?float
    {
        $this->checkDefaultType(function() { return is_float($this->default);}, 'float');
        return $this->default;
    }

    /**
     * read as string value
     *
     * @param   \stubbles\input\filter\range\StringLength  $length
     * @return  string
     */
    public function asString(StringLength $length = null): ?string
    {
        return $this->default;
    }

    /**
     * read as string value
     *
     * @param   \stubbles\input\filter\range\SecretMinLength  $length
     * @return  \stubbles\values\Secret
     */
    public function asSecret(SecretMinLength $length = null): ?Secret
    {
        $this->checkDefaultType(function() { return ($this->default instanceof Secret); }, Secret::class);
        return $this->default;
    }

    /**
     * read as text value
     *
     * @param   \stubbles\input\filter\range\StringLength  $length
     * @param   string[]                                   $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText(StringLength $length = null, array $allowedTags = []): ?string
    {
        return $this->default;
    }

    /**
     * read as json value
     *
     * @param   int  $maxLength  maximum allowed length of incoming JSON document in bytes  optional
     * @return  \stdClass|array|null
     */
    public function asJson(int $maxLength = JsonFilter::DEFAULT_MAX_LENGTH)
    {
        return $this->default;
    }

    /**
     * read as password value
     *
     * Default values for passwords make no sense, therefor all calls to this
     * method trigger a MethodNotSupportedException.
     *
     * @param   \stubbles\input\filter\PasswordChecker  $checker  checker to be used to ensure a good password
     * @return  \stubbles\values\Secret
     * @throws  \BadMethodCallException
     */
    public function asPassword(PasswordChecker $checker): ?Secret
    {
        throw new \BadMethodCallException('Default passwords are not supported');
    }

    /**
     * read as http uri
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * IllegalStateException will be thrown.
     *
     * @return  \stubbles\peer\http\HttpUri|null
     */
    public function asHttpUri()
    {
        $this->checkDefaultType(function() { return ($this->default instanceof \stubbles\peer\http\HttpUri); }, 'stubbles\peer\http\HttpUri');
        return $this->default;
    }

    /**
     * read as http uri if it does exist
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * IllegalStateException will be thrown.
     *
     * @return  \stubbles\peer\http\HttpUri|null
     */
    public function asExistingHttpUri()
    {
        $this->checkDefaultType(function() { return ($this->default instanceof \stubbles\peer\http\HttpUri); }, 'stubbles\peer\http\HttpUri');
        return $this->default;
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @return  string
     */
    public function asMailAddress(): ?string
    {
        return $this->default;
    }

    /**
     * read as date value
     *
     * In case the default value is not of type stubbles\date\Date an IllegalStateException
     * will be thrown.
     *
     * @param   \stubbles\input\filter\range\DateRange  $range
     * @return  \stubbles\date\Date|null
     */
    public function asDate(DateRange $range = null)
    {
        $this->default = (null === $this->default) ? (null) : (Date::castFrom($this->default, 'default'));
        return $this->default;
    }

    /**
     * read as day
     *
     * In case the default value is not of type stubbles\date\span\Day an
     * IllegalStateException will be thrown.
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Day|null
     */
    public function asDay(DatespanRange $range = null)
    {
        $this->checkDefaultType(function() { return $this->default instanceof Day;}, Day::class);
        return $this->default;
    }

    /**
     * read as week
     *
     * In case the default value is not of type stubbles\date\span\Week an
     * IllegalStateException will be thrown.
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Week|null
     * @since   4.5.0
     */
    public function asWeek(DatespanRange $range = null)
    {
        $this->checkDefaultType(function() { return $this->default instanceof Week;}, Week::class);
        return $this->default;
    }

    /**
     * read as month
     *
     * In case the default value is not of type stubbles\date\span\Month an
     * IllegalStateException will be thrown.
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Month|null
     */
    public function asMonth(DatespanRange $range = null)
    {
        $this->checkDefaultType(function() { return $this->default instanceof Month;}, Month::class);
        return $this->default;
    }

    /**
     * read as datespan
     *
     * In case the default value is not of type stubbles\date\span\Datespan an
     * IllegalStateException will be thrown.
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Datespan|null
     * @since   4.3.0
     */
    public function asDatespan(DatespanRange $range = null)
    {
        $this->checkDefaultType(function() { return $this->default instanceof Datespan;}, Datespan::class);
        return $this->default;
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @return  string
     */
    public function ifIsIpAddress(): ?string
    {
        return $this->default;
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param   string[]  $allowedValues  list of allowed values
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues): ?string
    {
        return $this->default;
    }

    /**
     * returns value if it is matched by given regular expression
     *
     * @param   string  $regex  regular expression to apply
     * @return  string
     * @since   6.0.0
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
     * @param   callable  $predicate  predicate to use
     * @param   string    $errorId    error id to be used in case validation fails
     * @param   array     $details    optional  details for param error in case validation fails
     * @return  string
     * @since   3.0.0
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
     *
     * @param   \stubbles\input\Filter  $filter
     * @return  mixed
     */
    public function withFilter(Filter $filter)
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
     *                   ->withCallable(function(Param $param)
     *                                  {
     *                                      if ($param->getValue() == 303) {
     *                                          return 'Roland TB-303';
     *                                      }
     *
     *                                      $param->addErrorWithId('INVALID_303');
     *                                      return null;
     *                                  }
     *                     );
     * </code>
     *
     * @param   callable  $filter
     * @return  mixed
     */
    public function withCallable(callable $filter)
    {
        return $this->default;
    }
}
