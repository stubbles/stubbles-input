<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;

use Closure;
use stdClass;
use stubbles\date\Date;
use stubbles\date\span\Datespan;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\date\span\Week;
use stubbles\input\errors\ParamErrors;
use stubbles\input\filter\{
    DateFilter,
    DatespanFilter,
    DayFilter,
    FloatFilter,
    IntegerFilter,
    JsonFilter,
    MailFilter,
    MonthFilter,
    PasswordChecker,
    PasswordFilter,
    PredicateFilter,
    range\DateRange,
    range\DatespanRange,
    range\SecretMinLength,
    range\StringLength,
    range\NumberRange,
    RangeFilter,
    SecretFilter,
    StringFilter,
    TextFilter,
    WeekFilter,
    WrapCallableFilter
};
use stubbles\input\valuereader\CommonValueReader;
use stubbles\input\valuereader\DefaultValueReader;
use stubbles\input\valuereader\MissingValueReader;
use stubbles\peer\http\HttpUri;
use stubbles\values\Parse;
use stubbles\values\Secret;
use stubbles\values\Value;
/**
 * Value object for request values to filter them or retrieve them after validation.
 *
 * @since  1.3.0
 */
class ValueReader implements valuereader\CommonValueReader
{
    public function __construct(
        private ParamErrors $paramErrors,
        private string $paramName,
        private Value $value
    ) { }

    /**
     * create instance as mock with empty param errors
     */
    public static function forValue(string|array|null $paramValue): self
    {
        return new self(new ParamErrors(), 'mock', Value::of($paramValue));
    }

    /**
     * enforce the value to be required
     *
     * @api
     */
    public function required(string $errorId = 'FIELD_EMPTY'): CommonValueReader
    {
        if ($this->value->isNull()) {
            return new MissingValueReader(
                function(string $actualErrorId): void
                {
                    $this->paramErrors->append($this->paramName, $actualErrorId);
                },
                $errorId
            );
        }

        return $this;
    }

    /**
     * sets a default value to be used in case param value is null
     *
     * It should be noted that some of the as*() methods check that the default
     * value is of the correct type. If it does not satisfy their type
     * requirements they will throw an IllegalStateException.
     *
     * @api
     * @param  mixed  $default  default value to use if no param value set
     */
    public function defaultingTo($default): CommonValueReader
    {
        if ($this->value->isNull()) {
            return new DefaultValueReader($default);
        }

        return $this;
    }

    /**
     * read as array value
     *
     * When input param is null return value is null, if input param is an empty
     * string return value is an empty array. For all other values the given param
     * will be split using the separator (defaults to ',') and each array element
     * will be trimmed to remove superfluous whitespace.
     *
     * @api
     * @param   string  $separator  optional  character to split input value with
     * @return  mixed[]
     * @since   2.0.0
     */
    public function asArray(string $separator = self::ARRAY_SEPARATOR): ?array
    {
        if ($this->value->isNull()) {
            return null;
        }

        $val = Parse::toList($this->value->value(), $separator);
        if (null === $val) {
            return null;
        }

        return array_map('trim', $val);
    }

    /**
     * read as boolean value
     *
     * @api
     * @since  1.7.0
     */
    public function asBool(): ?bool
    {
        if ($this->value->isNull()) {
            return null;
        }

        return $this->value->isOneOf([1, '1', 'true', true, 'yes'], true);
    }

    /**
     * read as integer value
     *
     * @api
     */
    public function asInt(?NumberRange $range = null): ?int
    {
        return $this->handleFilter(
            function() use($range): Filter
            {
                return RangeFilter::wrap(
                    IntegerFilter::instance(),
                    $range
                );
            }
        );
    }

    /**
     * read as float value
     *
     * @api
     */
    public function asFloat(?NumberRange $range = null, ?int $decimals = null): ?float
    {
        return $this->handleFilter(
            function() use($range, $decimals): Filter
            {
                return RangeFilter::wrap(
                    (new FloatFilter())->setDecimals($decimals),
                    $range
                );
            }
        );
    }

    /**
     * read as string value
     *
     * @api
     * @param   \stubbles\input\filter\range\StringLength  $length  optional  allowed length of string
     * @return  string
     */
    public function asString(?StringLength $length = null): ?string
    {
        return $this->handleFilter(
            function() use($length): Filter
            {
                return RangeFilter::wrap(
                    StringFilter::instance(),
                    $length
                );
            }
        );
    }

    /**
     * read as secret
     *
     * @api
     * @since  6.0.0
     */
    public function asSecret(?SecretMinLength $length = null): ?Secret
    {
        return $this->handleFilter(
            function() use($length): Filter
            {
                return RangeFilter::wrap(
                    SecretFilter::instance(),
                    $length
                );
            }
        );
    }

    /**
     * read as text value
     *
     * @api
     * @param  string[]  $allowedTags  optional  list of allowed tags
     */
    public function asText(?StringLength $length = null, array $allowedTags = []): ?string
    {
        return $this->handleFilter(
            function() use($length, $allowedTags): Filter
            {
                return RangeFilter::wrap(
                    (new TextFilter())->allowTags($allowedTags),
                    $length
                );
            }
        );
    }

    /**
     * read as json value
     *
     * @api
     * @param  int  $maxLength  maximum allowed length of incoming JSON document in bytes  optional
     */
    public function asJson(int $maxLength = filter\JsonFilter::DEFAULT_MAX_LENGTH): stdClass|array|null
    {
        return $this->withFilter(new JsonFilter($maxLength));
    }

    /**
     * read as password value
     *
     * @api
     */
    public function asPassword(PasswordChecker $checker): ?Secret
    {
        return $this->withFilter(new PasswordFilter($checker));
    }

    /**
     * read as http uri
     *
     * Return value is null in the following cases:
     * - Given param value is null or empty string.
     * - Given param value contains an invalid http uri.
     * In all other cases an instance of stubbles\peer\http\HttpUri is returned.
     *
     * @api
     */
    public function asHttpUri(): ?HttpUri
    {
        if ($this->value->isEmpty()) {
            return null;
        }

        if ($this->value->isHttpUri()) {
            return HttpUri::fromString($this->value->value());
        }

        $this->paramErrors->append($this->paramName, 'HTTP_URI_INCORRECT');
        return null;
    }

    /**
     * read as http uri if it does exist
     *
     * Return value is null in the following cases:
     * - Given param value is null or empty string.
     * - Given param value contains an invalid http uri.
     * - Given http uri doesn't have a DNS record but DNS record is enforced.
     * In all other cases an instance of stubbles\peer\http\HttpUri is returned.
     *
     * @api
     * @param  callable  $checkdnsrr  optional  function with which to check DNS record, defaults to checkdnsrr()
     */
    public function asExistingHttpUri(?callable $checkdnsrr = null): ?HttpUri
    {
        $httpUri = $this->asHttpUri();
        if (null === $httpUri) {
            return null;
        }

        if ($httpUri->hasDnsRecord($checkdnsrr)) {
            return $httpUri;
        }

        $this->paramErrors->append($this->paramName, 'HTTP_URI_NOT_AVAILABLE');
        return null;
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @api
     */
    public function asMailAddress(): ?string
    {
        return $this->withFilter(MailFilter::instance());
    }

    /**
     * read as date value
     *
     * @api
     */
    public function asDate(?DateRange $range = null): ?Date
    {
        return $this->handleFilter(
            function() use($range): Filter
            {
                return RangeFilter::wrap(
                    DateFilter::instance(),
                    $range
                );
            }
        );
    }

    /**
     * read as day
     *
     * @api
     * @since  2.0.0
     */
    public function asDay(?DatespanRange $range = null): ?Day
    {
        return $this->handleFilter(
            function() use($range): Filter
            {
                return RangeFilter::wrap(
                    DayFilter::instance(),
                    $range
                );
            }
        );
    }

    /**
     * read as week
     *
     * @api
     * @since  4.5.0
     */
    public function asWeek(?DatespanRange $range = null): ?Week
    {
        return $this->handleFilter(
            function() use($range): Filter
            {
                return RangeFilter::wrap(
                    WeekFilter::instance(),
                    $range
                );
            }
        );
    }

    /**
     * read as month
     *
     * @api
     * @since   2.5.1
     */
    public function asMonth(?DatespanRange $range = null): ?Month
    {
        return $this->handleFilter(
            function() use($range): Filter
            {
                return RangeFilter::wrap(
                    MonthFilter::instance(),
                    $range
                );
            }
        );
    }

    /**
     * read as datespan
     *
     * @since   4.3.0
     */
    public function asDatespan(?DatespanRange $range = null): ?Datespan
    {
        return $this->handleFilter(
            function() use($range): Filter
            {
                return RangeFilter::wrap(
                    DatespanFilter::instance(),
                    $range
                );
            }
        );
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @api
     */
    public function ifIsIpAddress(): ?string
    {
        if ($this->value->isIpAddress()) {
            return $this->value->value();
        }

        $this->paramErrors->append($this->paramName, 'INVALID_IP_ADDRESS');
        return null;
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @api
     * @param  string[]  $allowedValues  list of allowed values
     */
    public function ifIsOneOf(array $allowedValues): ?string
    {
        if ($this->value->isOneOf($allowedValues)) {
            return $this->value->value();
        }

        $this->paramErrors->append(
            $this->paramName,
            'FIELD_NO_SELECT',
            ['ALLOWED' => join('|', $allowedValues)]
        );
        return null;
    }

    /**
     * returns value if it is matched by given regular expression
     *
     * @api
     * @since  6.0.0
     */
    public function ifMatches(string $regex): ?string
    {
        if ($this->value->isMatchedBy($regex)) {
            return $this->value->value();
        }

        $this->paramErrors->append($this->paramName, 'FIELD_WRONG_VALUE');
        return null;
    }

    /**
     * returns param value when given predicate evaluates to true
     *
     * If value does not satisfy the predicate return value will be null.
     *
     * @api
     * @param   callable              $predicate  predicate to use
     * @param   string                $errorId    error id to be used in case validation fails
     * @param   array<string,scalar>  $details    optional  details for param error in case validation fails
     * @return  string
     * @since   3.0.0
     */
    public function when(callable $predicate, string $errorId, array $details = []): ?string
    {
        return $this->handleFilter(
            fn(): Filter => new PredicateFilter($predicate, $errorId, $details)
        );
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * @api
     */
    public function withFilter(Filter $filter): mixed
    {
        if ($this->value->isNull()) {
            return null;
        }

        return $this->applyFilter($filter);
    }

    private function handleFilter(Closure $createFilter): mixed
    {
        if ($this->value->isNull()) {
            return null;
        }

        return $this->applyFilter($createFilter());
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     */
    private function applyFilter(Filter $filter): mixed
    {
        list($filtered, $errors) = $filter->apply($this->value);
        if (count($errors) === 0) {
            return $filtered;
        }

        foreach ($errors as $error) {
            $this->paramErrors->append($this->paramName, $error);
        }

        return null;
    }

    /**
     * checks value with given callable
     *
     * The callable must accept an instance of stubbles\input\Param and
     * return the filtered value. It can add errors to the provided param when
     * the param value is not satisfying.
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
     *
     * @api
     * @since  3.0.0
     */
    public function withCallable(callable $filter): mixed
    {
        if ($this->value->isNull()) {
            return null;
        }

        return $this->applyFilter(new WrapCallableFilter($filter));
    }

    /**
     * returns value unvalidated
     *
     * This should be used with greatest care.
     *
     * @api
     */
    public function unsecure(): ?string
    {
        return $this->value->value();
    }
}
