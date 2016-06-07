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
use stubbles\input\errors\ParamErrors;
use stubbles\input\filter\ArrayFilter;
use stubbles\input\filter\PasswordChecker;
use stubbles\input\filter\range\DateRange;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\filter\range\StringLength;
use stubbles\input\filter\range\NumberRange;
use stubbles\peer\IpAddress;

use function stubbles\input\predicate\isOneOf;
use function stubbles\values\pattern;
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
     * @type  \stubbles\input\errors\ParamErrors
     */
    private $paramErrors;
    /**
     * parameter to filter
     *
     * @type  \stubbles\input\Param
     */
    private $param;

    /**
     * constructor
     *
     * @param  \stubbles\input\errors\ParamErrors  $paramErrors  list of errors to add any filter errors to
     * @param  \stubbles\input\Param               $param        parameter to filter
     */
    public function __construct(ParamErrors $paramErrors, Param $param)
    {
        $this->paramErrors = $paramErrors;
        $this->param       = $param;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string  $paramValue  actual value to use
     * @return  \stubbles\input\ValueReader
     */
    public static function forValue($paramValue)
    {
        return new self(new ParamErrors(), new Param('mock', $paramValue));
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   \stubbles\input\Param  $param  param to use
     * @return  \stubbles\input\ValueReader
     */
    public static function forParam(Param $param)
    {
        return new self(new ParamErrors(), $param);
    }

    /**
     * enforce the value to be required
     *
     * @api
     * @param   string  $errorId  optional  error id to use when value not set
     * @return  \stubbles\input\valuereader\CommonValueReader
     */
    public function required($errorId = 'FIELD_EMPTY')
    {
        if ($this->param->isNull()) {
            return new valuereader\MissingValueReader(
                    function($actualErrorId)
                    {
                        $this->paramErrors->append($this->param->name(), $actualErrorId);
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
    public function defaultingTo($default)
    {
        if ($this->param->isNull()) {
            return new valuereader\DefaultValueReader($default);
        }

        return $this;
    }

    /**
     * read as array value
     *
     * @api
     * @param   string  $separator  optional  character to split input value with
     * @return  array
     * @since   2.0.0
     */
    public function asArray($separator = ArrayFilter::SEPARATOR_DEFAULT)
    {
        return $this->handleFilter(
                function() use($separator) { return new ArrayFilter($separator); }
        );
    }

    /**
     * read as boolean value
     *
     * @api
     * @return  bool
     * @since   1.7.0
     */
    public function asBool()
    {
        return $this->withFilter(filter\BoolFilter::instance());
    }

    /**
     * read as integer value
     *
     * @api
     * @param   \stubbles\input\filter\range\NumberRange  $range  optional  range of allowed values
     * @return  int
     */
    public function asInt(NumberRange $range = null)
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
    public function asFloat(NumberRange $range = null, $decimals = null)
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
    public function asString(StringLength $length = null)
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
     * read as string value
     *
     * @api
     * @param   \stubbles\input\filter\range\StringLength  $length  optional  allowed length of string
     * @return  \stubbles\values\Secret
     * @since   3.0.0
     * @deprecated  since 6.0.0, use asSecret() instead, will be removed with 7.0.0
     */
    public function asSecureString(StringLength $length = null)
    {
        return $this->asSecret($length);
    }

    /**
     * read as secret
     *
     * @api
     * @param   \stubbles\input\filter\range\StringLength  $length  optional  allowed length of string
     * @return  \stubbles\values\Secret
     * @since   6.0.0
     */
    public function asSecret(StringLength $length = null)
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
    public function asText(StringLength $length = null, $allowedTags = [])
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
     * @return  \stdClass|array
     */
    public function asJson()
    {
        return $this->withFilter(filter\JsonFilter::instance());
    }

    /**
     * read as password value
     *
     * @api
     * @param   \stubbles\input\filter\PasswordChecker  $checker  checker to be used to ensure a good password
     * @return  \stubbles\values\Secret
     */
    public function asPassword(PasswordChecker $checker)
    {
        return $this->withFilter(new filter\PasswordFilter($checker));
    }

    /**
     * read as http uri
     *
     * @api
     * @return  \stubbles\peer\http\HttpUri
     */
    public function asHttpUri()
    {
        return $this->handleFilter(
                function() { return filter\HttpUriFilter::instance(); }
        );
    }

    /**
     * read as http uri if it does exist
     *
     * @api
     * @return  \stubbles\peer\http\HttpUri
     */
    public function asExistingHttpUri()
    {
        return $this->handleFilter(
                function() { return filter\ExistingHttpUriFilter::instance(); }
        );
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @api
     * @return  string
     */
    public function asMailAddress()
    {
        return $this->withFilter(filter\MailFilter::instance());
    }

    /**
     * read as date value
     *
     * @api
     * @param   \stubbles\input\filter\range\DateRange  $range  optional  allowed range of allowed dates
     * @return  \stubbles\date\Date
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
     * @return  \stubbles\date\span\Day
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
     * @return  \stubbles\date\span\Week
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
     * @return  \stubbles\date\span\Month
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
     * @return  \stubbles\date\span\Datespan
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
    public function ifIsIpAddress()
    {
        if (IpAddress::isValid($this->param->value())) {
            return $this->param->value();
        }

        $this->paramErrors->append($this->param->name(), 'INVALID_IP_ADDRESS');
        return null;
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @api
     * @param   string[]  $allowedValues  list of allowed values
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues)
    {
        if (isOneOf($allowedValues)->test($this->param->value())) {
            return $this->param->value();
        }

        $this->paramErrors->append(
                $this->param->name(),
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
    public function ifMatches($regex)
    {
        if (pattern($regex)->matches($this->param->value())) {
            return $this->param->value();
        }

        $this->paramErrors->append($this->param->name(), 'FIELD_WRONG_VALUE');
        return null;
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @api
     * @param   string  $regex  regular expression to apply
     * @return  string
     * @deprecated  since 6.0.0, use ifMatches() instead, will be removed with 7.0.0
     */
    public function ifSatisfiesRegex($regex)
    {
        return $this->ifMatches($regex);
    }

    /**
     * returns param value when given predicate evaluates to true
     *
     * If value does not satisfy the predicate return value will be null.
     *
     * @api
     * @param   \stubbles\input\predicate\Predicate|callable  $predicate  predicate to use
     * @param   string                                        $errorId    error id to be used in case validation fails
     * @param   array                                         $details    optional  details for param error in case validation fails
     * @return  string
     * @since   3.0.0
     */
    public function when($predicate, $errorId, array $details = [])
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
        if ($this->param->isNull()) {
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
        if ($this->param->isNull()) {
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
        $value = $filter->apply($this->param);
        if (!$this->param->hasErrors()) {
            return $value;
        }

        foreach ($this->param->errors() as $error) {
            $this->paramErrors->append($this->param->name(), $error);
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
     *                   ->withCallable(function(Param $param)
     *                                  {
     *                                      if ($param->value() == 303) {
     *                                          return 'Roland TB-303';
     *                                      }
     *
     *                                      $param->addError('INVALID_303');
     *                                      return null;
     *                                  }
     *                     );
     * </code>
     *
     * @api
     * @param   callable  $filter  function to apply for reading the value
     * @return  mixed
     * @since   3.0.0
     */
    public function withCallable(callable $filter)
    {
        if ($this->param->isNull()) {
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
    public function unsecure()
    {
        return $this->param->value();
    }
}
