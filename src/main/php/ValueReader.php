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
use stubbles\input\filter\PasswordFilter;
use stubbles\input\filter\range\DateRange;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\filter\range\StringLength;
use stubbles\input\filter\range\NumberRange;
use stubbles\peer\http\HttpUri;
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
     * @type  ParamErrors
     */
    private $paramErrors;
    /**
     * parameter to filter
     *
     * @type  Param
     */
    private $param;

    /**
     * constructor
     *
     * @param  ParamErrors  $paramErrors  list of errors to add any filter errors to
     * @param  Param        $param        parameter to filter
     */
    public function __construct(ParamErrors $paramErrors, Param $param)
    {
        $this->paramErrors = $paramErrors;
        $this->param       = $param;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string  $paramValue
     * @return  ValueReader
     */
    public static function forValue($paramValue)
    {
        return new self(new ParamErrors(), new Param('mock', $paramValue));
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   Param  $param
     * @return  ValueReader
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
     * @param   mixed  $default
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
        return $this->handleFilter(function() use($separator) { return new ArrayFilter($separator); } );
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
        return $this->applyFilter(new filter\BoolFilter());
    }

    /**
     * read as integer value
     *
     * @api
     * @param   NumberRange  $range
     * @return  int
     */
    public function asInt(NumberRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\IntegerFilter(),
                                                                       $range
                                       );
                                   }
        );
    }

    /**
     * read as float value
     *
     * @api
     * @param   NumberRange  $range
     * @param   int          $decimals  number of decimals
     * @return  float
     */
    public function asFloat(NumberRange $range = null, $decimals = null)
    {
        return $this->handleFilter(function() use($range, $decimals)
                                   {
                                       $floatFilter = new filter\FloatFilter();
                                       return filter\RangeFilter::wrap($floatFilter->setDecimals($decimals),
                                                                       $range
                                       );
                                   }
        );
    }

    /**
     * read as string value
     *
     * @api
     * @param   StringLength  $length
     * @return  string
     */
    public function asString(StringLength $length = null)
    {
        return $this->handleFilter(function() use($length)
                                   {
                                       return filter\RangeFilter::wrap(new filter\StringFilter(),
                                                                       $length
                                       );
                                   }
        );
    }

    /**
     * read as string value
     *
     * @api
     * @param   StringLength  $length
     * @return  \stubbles\lang\SecureString
     * @since   3.0.0
     */
    public function asSecureString(StringLength $length = null)
    {
        return $this->handleFilter(function() use($length)
                                   {
                                       return filter\RangeFilter::wrap(new filter\SecureStringFilter(),
                                                                       $length
                                       );
                                   }
        );
    }

    /**
     * read as text value
     *
     * @api
     * @param   StringLength  $length
     * @param   string[]      $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText(StringLength $length = null, $allowedTags = [])
    {
        return $this->handleFilter(function() use($length, $allowedTags)
                                   {
                                       $textFilter = new filter\TextFilter();
                                       return filter\RangeFilter::wrap($textFilter->allowTags($allowedTags),
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
        return $this->handleFilter(function() { return new filter\JsonFilter(); });
    }

    /**
     * read as password value
     *
     * @api
     * @param   int       $minDiffChars      minimum amount of different characters within password
     * @param   string[]  $nonAllowedValues  list of values that are not allowed as password
     * @return  \stubbles\lang\SecureString
     */
    public function asPassword($minDiffChars = PasswordFilter::MIN_DIFF_CHARS_DEFAULT, array $nonAllowedValues = [])
    {
        $passWordFilter = new PasswordFilter();
        return $this->withFilter($passWordFilter->minDiffChars($minDiffChars)
                                                ->disallowValues($nonAllowedValues)
        );
    }

    /**
     * read as http uri
     *
     * @api
     * @return  HttpUri
     */
    public function asHttpUri()
    {
        return $this->handleFilter(function() { return new filter\HttpUriFilter(); });
    }

    /**
     * read as http uri if it does exist
     *
     * @api
     * @return  HttpUri
     */
    public function asExistingHttpUri()
    {
        return $this->handleFilter(function()
                                   {
                                       $httpUriFilter = new filter\HttpUriFilter();
                                       return $httpUriFilter->enforceDnsRecord();
                                   }
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
        return $this->handleFilter(function() { return new filter\MailFilter(); });
    }

    /**
     * read as date value
     *
     * @api
     * @param   DateRange                  $range
     * @return  \stubbles\date\Date
     */
    public function asDate(DateRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\DateFilter(),
                                                                       $range
                                       );
                                   }
        );
    }

    /**
     * read as day
     *
     * @api
     * @param   DatespanRange  $range
     * @return  \stubbles\date\span\Day
     * @since   2.0.0
     */
    public function asDay(DatespanRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\DayFilter(),
                                                                       $range
                                       );
                                   }
        );
    }

    /**
     * read as month
     *
     * @api
     * @param   DatespanRange  $range
     * @return  \stubbles\date\span\Month
     * @since   2.5.1
     */
    public function asMonth(DatespanRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\MonthFilter(),
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
        return $this->withValidator(new validator\IpValidator(),
                                    'INVALID_IP_ADDRESS',
                                    []
        );
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
        return $this->withValidator(new validator\PreSelectValidator($allowedValues),
                                    'FIELD_NO_SELECT',
                                    ['ALLOWED' => join('|', $allowedValues)]
        );
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @api
     * @param   string  $regex    regular expression to apply
     * @return  string
     */
    public function ifSatisfiesRegex($regex)
    {
        return $this->withValidator(new validator\RegexValidator($regex),
                                    'FIELD_WRONG_VALUE',
                                    []
        );
    }

    /**
     * returns value if it denotes a path to an existing file, and null otherwise
     *
     * This should be used with greatest care in web environments as it only
     * checks if the file exists, but not if there are any rights to access
     * the specific file. It also does not prevent constructions which would
     * allow an attacker to reach e.g. /etc/passwd via ../../ constructions.
     *
     * @api
     * @param   string  $basePath  base path where file must reside in
     * @return  string
     * @since   2.0.0
     */
    public function ifIsFile($basePath = null)
    {
        $path = ((null != $basePath) ? ($basePath . '/') : (''));
        return $this->withValidator(new validator\FileValidator($basePath),
                                    'FILE_INVALID',
                                    ['PATH' => $path . $this->param->value()]
        );
    }

    /**
     * returns value if it denotes a path to an existing directory, and null otherwise
     *
     * This should be used with greatest care in web environments as it only
     * checks if the directory exists, but not if there are any rights to access
     * the specific directory. It also does not prevent constructions which would
     * allow an attacker to reach a certain directory via ../../ constructions.
     *
     * @api
     * @param   string  $basePath  base path where directory must reside in
     * @return  string
     * @since   2.0.0
     */
    public function ifIsDirectory($basePath = null)
    {
        $path = ((null != $basePath) ? ($basePath . '/') : (''));
        return $this->withValidator(new validator\DirectoryValidator($basePath),
                                    'DIRECTORY_INVALID',
                                    ['PATH' => $path . $this->param->value()]
        );
    }


    /**
     * checks value with given validator
     *
     * If value does not satisfy the validator return value will be null.
     *
     * @api
     * @param   Validator  $validator  validator to use
     * @param   string     $errorId    error id to be used in case validation fails
     * @param   array      $details    optional  details for param error in case validation fails
     * @return  string
     */
    public function withValidator(Validator $validator, $errorId, array $details = [])
    {
        return $this->handleFilter(function() use($validator, $errorId, $details)
                                   {
                                       return new filter\ValidatingFilter($validator, $errorId, $details);
                                   }
        );
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * If it is required but value is null an error will be added to the list
     * of param errors.
     *
     * @api
     * @param   Filter  $filter
     * @return  mixed
     */
    public function withFilter(Filter $filter)
    {
        return $this->handleFilter(function() use ($filter) { return $filter;});
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
     * @api
     * @param   Filter  $filter
     * @return  mixed
     */
    public function applyFilter(Filter $filter)
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
     * @api
     * @param   callable  $filter
     * @return  mixed
     * @since   3.0.0
     */
    public function withCallable(callable $filter)
    {
        if ($this->param->isNull()) {
            return null;
        }

        $value = $filter($this->param);
        if (!$this->param->hasErrors()) {
            return $value;
        }

        foreach ($this->param->errors() as $error) {
            $this->paramErrors->append($this->param->name(), $error);
        }

        return null;
    }

    /**
     * checks value with given closure
     *
     * The closure must accept an instance of stubbles\input\Param and
     * return the filtered value.
     * <code>
     * $result = $request->readParam('name')
     *                   ->withFunction(function(Param $param)
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
     * @api
     * @since   2.2.0
     * @param   \Closure  $filter
     * @return  mixed
     * @deprecated  since 3.0.0, use withCallable() instead, will be removed with 4.0.0
     */
    public function withFunction(\Closure $filter)
    {
        return $this->withCallable($filter);
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
