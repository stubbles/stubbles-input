<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use stubbles\input\errors\ParamErrors;
use stubbles\input\filter\{
    ArrayFilter,
    PasswordChecker,
    range\DateRange,
    range\DatespanRange,
    range\SecretMinLength,
    range\StringLength,
    range\NumberRange
};
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
    /**
     * request instance the value inherits from
     *
     * @var  \stubbles\input\errors\ParamErrors
     */
    private $paramErrors;
    /**
     * name of parameter to read
     *
     * @var  string
     */
    private $paramName;
    /**
     * parameter to filter
     *
     * @var  \stubbles\values\Value
     */
    private $value;

    /**
     * constructor
     *
     * @param  \stubbles\input\errors\ParamErrors  $paramErrors  list of errors to add any filter errors to
     * @param  string                              $paramName    name of parameter to read
     * @param  \stubbles\values\Value              $value        value to read
     */
    public function __construct(
            ParamErrors $paramErrors,
            string $paramName,
            Value $value
    ) {
        $this->paramErrors = $paramErrors;
        $this->paramName   = $paramName;
        $this->value       = $value;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string|string[]|null  $paramValue  actual value to use
     * @return  \stubbles\input\ValueReader
     */
    public static function forValue($paramValue): self
    {
        return new self(new ParamErrors(), 'mock', Value::of($paramValue));
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   \stubbles\input\Param  $param  param to use
     * @return  \stubbles\input\ValueReader
     * @deprecated  since 7.0.0, will be removed with 8.0.0
     */
    public static function forParam(Param $param): self
    {
        return self::forValue($param->value());
    }

    /**
     * enforce the value to be required
     *
     * @api
     * @param   string  $errorId  optional  error id to use when value not set
     * @return  \stubbles\input\valuereader\CommonValueReader
     */
    public function required(string $errorId = 'FIELD_EMPTY'): valuereader\CommonValueReader
    {
        if ($this->value->isNull()) {
            return new valuereader\MissingValueReader(
                    function($actualErrorId)
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
     * @param   mixed  $default  default value to use if no param value set
     * @return  \stubbles\input\valuereader\CommonValueReader
     */
    public function defaultingTo($default): valuereader\CommonValueReader
    {
        if ($this->value->isNull()) {
            return new valuereader\DefaultValueReader($default);
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
     * @return  bool
     * @since   1.7.0
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
     * @param   \stubbles\input\filter\range\NumberRange  $range  optional  range of allowed values
     * @return  int
     */
    public function asInt(NumberRange $range = null): ?int
    {
        return $this->handleFilter(
                function() use($range)
                {
                    return filter\RangeFilter::wrap(
                            filter\IntegerFilter::instance(),
                            $range
                    );
                }
        );
    }

    /**
     * read as float value
     *
     * @api
     * @param   \stubbles\input\filter\range\NumberRange  $range     optional  range of allowed values
     * @param   int                                       $decimals  optional  number of decimals
     * @return  float
     */
    public function asFloat(NumberRange $range = null, int $decimals = null): ?float
    {
        return $this->handleFilter(
                function() use($range, $decimals)
                {
                    $floatFilter = new filter\FloatFilter();
                    return filter\RangeFilter::wrap(
                            $floatFilter->setDecimals($decimals),
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
    public function asString(StringLength $length = null): ?string
    {
        return $this->handleFilter(
                function() use($length)
                {
                    return filter\RangeFilter::wrap(
                            filter\StringFilter::instance(),
                            $length
                    );
                }
        );
    }

    /**
     * read as secret
     *
     * @api
     * @param   \stubbles\input\filter\range\SecretMinLength  $length  optional  required min length of string
     * @return  \stubbles\values\Secret
     * @since   6.0.0
     */
    public function asSecret(SecretMinLength $length = null): ?Secret
    {
        return $this->handleFilter(
                function() use($length)
                {
                    return filter\RangeFilter::wrap(
                            filter\SecretFilter::instance(),
                            $length
                    );
                }
        );
    }

    /**
     * read as text value
     *
     * @api
     * @param   \stubbles\input\filter\range\StringLength  $length       optional  allowed length of text
     * @param   string[]                                   $allowedTags  optional  list of allowed tags
     * @return  string
     */
    public function asText(StringLength $length = null, array $allowedTags = []): ?string
    {
        return $this->handleFilter(
                function() use($length, $allowedTags)
                {
                    $textFilter = new filter\TextFilter();
                    return filter\RangeFilter::wrap(
                            $textFilter->allowTags($allowedTags),
                            $length
                    );
                }
        );
    }

    /**
     * read as json value
     *
     * @api
     * @param   int  $maxLength  maximum allowed length of incoming JSON document in bytes  optional
     * @return  \stdClass|array<mixed>|null
     */
    public function asJson(int $maxLength = filter\JsonFilter::DEFAULT_MAX_LENGTH)
    {
        return $this->withFilter(new filter\JsonFilter($maxLength));
    }

    /**
     * read as password value
     *
     * @api
     * @param   \stubbles\input\filter\PasswordChecker  $checker  checker to be used to ensure a good password
     * @return  \stubbles\values\Secret
     */
    public function asPassword(PasswordChecker $checker): ?Secret
    {
        return $this->withFilter(new filter\PasswordFilter($checker));
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
     * @return  \stubbles\peer\http\HttpUri|null
     */
    public function asHttpUri()
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
     * @param   callable  $checkdnsrr  optional  function with which to check DNS record, defaults to checkdnsrr()
     * @return  \stubbles\peer\http\HttpUri|null
     */
    public function asExistingHttpUri(callable $checkdnsrr = null)
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
     * @return  string
     */
    public function asMailAddress(): ?string
    {
        return $this->withFilter(filter\MailFilter::instance());
    }

    /**
     * read as date value
     *
     * @api
     * @param   \stubbles\input\filter\range\DateRange  $range  optional  allowed range of allowed dates
     * @return  \stubbles\date\Date|null
     */
    public function asDate(DateRange $range = null)
    {
        return $this->handleFilter(
                function() use($range)
                {
                    return filter\RangeFilter::wrap(
                            filter\DateFilter::instance(),
                            $range
                    );
                }
        );
    }

    /**
     * read as day
     *
     * @api
     * @param   \stubbles\input\filter\range\DatespanRange  $range  optional  range where the day must be within
     * @return  \stubbles\date\span\Day|null
     * @since   2.0.0
     */
    public function asDay(DatespanRange $range = null)
    {
        return $this->handleFilter(
                function() use($range)
                {
                    return filter\RangeFilter::wrap(
                            filter\DayFilter::instance(),
                            $range
                    );
                }
        );
    }

    /**
     * read as week
     *
     * @api
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Week|null
     * @since   4.5.0
     */
    public function asWeek(DatespanRange $range = null)
    {
        return $this->handleFilter(
                function() use($range)
                {
                    return filter\RangeFilter::wrap(
                            filter\WeekFilter::instance(),
                            $range
                    );
                }
        );
    }

    /**
     * read as month
     *
     * @api
     * @param   \stubbles\input\filter\range\DatespanRange  $range  optional  range where the month must be within
     * @return  \stubbles\date\span\Month|null
     * @since   2.5.1
     */
    public function asMonth(DatespanRange $range = null)
    {
        return $this->handleFilter(
                function() use($range)
                {
                    return filter\RangeFilter::wrap(
                            filter\MonthFilter::instance(),
                            $range
                    );
                }
        );
    }

    /**
     * read as datespan

     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Datespan|null
     * @since   4.3.0
     */
    public function asDatespan(DatespanRange $range = null)
    {
        return $this->handleFilter(
                function() use($range)
                {
                    return filter\RangeFilter::wrap(
                            filter\DatespanFilter::instance(),
                            $range
                    );
                }
        );
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @api
     * @return  string
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
     * @param   string[]  $allowedValues  list of allowed values
     * @return  string
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
     * @param   string  $regex  regular expression to apply
     * @return  string
     * @since   6.0.0
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
                function() use($predicate, $errorId, $details)
                {
                    return new filter\PredicateFilter($predicate, $errorId, $details);
                }
        );
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * @api
     * @param   \stubbles\input\Filter  $filter  filter to apply
     * @return  mixed
     */
    public function withFilter(Filter $filter)
    {
        if ($this->value->isNull()) {
            return null;
        }

        return $this->applyFilter($filter);
    }

    /**
     * handles a filter
     *
     * @param   \Closure  $createFilter
     * @return  mixed
     */
    private function handleFilter(\Closure $createFilter)
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
     *
     * @param   \stubbles\input\Filter  $filter
     * @return  mixed
     */
    private function applyFilter(Filter $filter)
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
     * $result = $request->readParam('name')->withCallable(
     *         function(Value $value, array &$errors)
     *         {
     *             if ($value->equals(303)) {
     *                 return 'Roland TB-303';
     *             }
     *
     *             $errors['INVALID_303'] = [];
     *             return null;
     *         }
     * );
     * </code>
     *
     * @api
     * @param   callable  $filter  function to apply for reading the value
     * @return  mixed
     * @since   3.0.0
     */
    public function withCallable(callable $filter)
    {
        if ($this->value->isNull()) {
            return null;
        }

        return $this->applyFilter(new filter\WrapCallableFilter($filter));
    }

    /**
     * returns value unvalidated
     *
     * This should be used with greatest care.
     *
     * @api
     * @return  string
     */
    public function unsecure(): ?string
    {
        return $this->value->value();
    }
}
