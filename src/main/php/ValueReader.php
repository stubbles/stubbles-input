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
use stubbles\date\Date;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
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
class ValueReader
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
     * switch whether value is required
     *
     * @type  bool
     */
    private $required        = false;
    /**
     * error id to be used if param is required but empty
     *
     * @type  string
     */
    private $requiredErrorId = 'FIELD_EMPTY';

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
     * whether value is required or not
     *
     * @param   string  $errorId
     * @return  ValueReader
     */
    public function required($errorId = 'FIELD_EMPTY')
    {
        $this->required        = true;
        $this->requiredErrorId = $errorId;
        return $this;
    }

    /**
     * read as array value
     *
     * @api
     * @param   array   $default    optional
     * @param   string  $separator  optional  character to split input value with
     * @return  array
     * @since   2.0.0
     */
    public function asArray(array $default = null, $separator = ArrayFilter::SEPARATOR_DEFAULT)
    {
        return $this->handleFilter(function() use($separator)
                                   {
                                       $arrayFilter = new ArrayFilter();
                                       return $arrayFilter->setSeparator($separator);
                                   },
                                   $default
        );
    }

    /**
     * read as boolean value
     *
     * @api
     * @param   bool  $default  default value to fall back to
     * @return  bool
     * @since   1.7.0
     */
    public function asBool($default = null)
    {
        if ($this->param->isNull() && null !== $default) {
            return $default;
        }

        return $this->applyFilter(new filter\BoolFilter());
    }

    /**
     * read as integer value
     *
     * @api
     * @param   int          $default
     * @param   NumberRange  $range
     * @return  int
     */
    public function asInt($default = null, NumberRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\IntegerFilter(),
                                                                       $range
                                       );
                                   },
                                   $default
        );
    }

    /**
     * read as float value
     *
     * @api
     * @param   int          $default
     * @param   NumberRange  $range
     * @param   int          $decimals  number of decimals
     * @return  float
     */
    public function asFloat($default = null, NumberRange $range = null, $decimals = null)
    {
        return $this->handleFilter(function() use($range, $decimals)
                                   {
                                       $floatFilter = new filter\FloatFilter();
                                       return filter\RangeFilter::wrap($floatFilter->setDecimals($decimals),
                                                                       $range
                                       );
                                   },
                                   $default
        );
    }

    /**
     * read as string value
     *
     * @api
     * @param   string        $default
     * @param   StringLength  $length
     * @return  string
     */
    public function asString($default = null, StringLength $length = null)
    {
        return $this->handleFilter(function() use($length)
                                   {
                                       return filter\RangeFilter::wrap(new filter\StringFilter(),
                                                                       $length
                                       );
                                   },
                                   $default
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
     * @param   string        $default
     * @param   StringLength  $length
     * @param   string[]      $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText($default = null, StringLength $length = null, $allowedTags = [])
    {
        return $this->handleFilter(function() use($length, $allowedTags)
                                   {
                                       $textFilter = new filter\TextFilter();
                                       return filter\RangeFilter::wrap($textFilter->allowTags($allowedTags),
                                                                       $length
                                       );
                                   },
                                   $default
        );
    }

    /**
     * read as json value
     *
     * @api
     * @param   mixed  $default
     * @return  \stdClass|array
     */
    public function asJson($default = null)
    {
        return $this->handleFilter(function()
                                   {
                                       return new filter\JsonFilter();
                                   },
                                   $default
        );
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
     * @param   HttpUri  $default
     * @return  HttpUri
     */
    public function asHttpUri(HttpUri $default = null)
    {
        if ('FIELD_EMPTY' === $this->requiredErrorId) {
            $this->requiredErrorId = 'HTTP_URI_MISSING';
        }

        return $this->handleFilter(function()
                                   {
                                       return new filter\HttpUriFilter();
                                   },
                                   $default
        );
    }

    /**
     * read as http uri if it does exist
     *
     * @api
     * @param   HttpUri  $default
     * @return  HttpUri
     */
    public function asExistingHttpUri(HttpUri $default = null)
    {
        if ('FIELD_EMPTY' === $this->requiredErrorId) {
            $this->requiredErrorId = 'HTTP_URI_MISSING';
        }

        return $this->handleFilter(function()
                                   {
                                       $httpUriFilter = new filter\HttpUriFilter();
                                       return $httpUriFilter->enforceDnsRecord();
                                   },
                                   $default
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
        if ('FIELD_EMPTY' === $this->requiredErrorId) {
            $this->requiredErrorId = 'MAILADDRESS_MISSING';
        }

        return $this->handleFilter(function()
                                   {
                                       return new filter\MailFilter();
                                   }
        );
    }

    /**
     * read as date value
     *
     * @api
     * @param   int|string|\DateTime|Date  $default
     * @param   DateRange                  $range
     * @return  stubbles\date\Date

     */
    public function asDate($default = null, DateRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\DateFilter(),
                                                                       $range
                                       );
                                   },
                                   (null === $default) ? (null) : (Date::castFrom($default, 'default'))
        );
    }

    /**
     * read as day
     *
     * @api
     * @param   Day            $default
     * @param   DatespanRange  $range
     * @return  stubbles\date\span\Day
     * @since   2.0.0

     */
    public function asDay(Day $default = null, DatespanRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\DayFilter(),
                                                                       $range
                                       );
                                   },
                                   $default
        );
    }

    /**
     * read as month
     *
     * @api
     * @param   Month          $default
     * @param   DatespanRange  $range
     * @return  stubbles\date\span\Month
     * @since   2.5.1
     */
    public function asMonth(Month $default = null, DatespanRange $range = null)
    {
        return $this->handleFilter(function() use($range)
                                   {
                                       return filter\RangeFilter::wrap(new filter\MonthFilter(),
                                                                       $range
                                       );
                                   },
                                   $default
        );
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @api
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifIsIpAddress($default = null)
    {
        return $this->withValidator(new validator\IpValidator(),
                                    'INVALID_IP_ADDRESS',
                                    [],
                                    $default
        );
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @api
     * @param   string[]  $allowedValues  list of allowed values
     * @param   string    $default        default value to fall back to
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues, $default = null)
    {
        return $this->withValidator(new validator\PreSelectValidator($allowedValues),
                                    'FIELD_NO_SELECT',
                                    ['ALLOWED' => join('|', $allowedValues)],
                                    $default
        );
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @api
     * @param   string  $regex    regular expression to apply
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifSatisfiesRegex($regex, $default = null)
    {
        return $this->withValidator(new validator\RegexValidator($regex),
                                    'FIELD_WRONG_VALUE',
                                    [],
                                    $default
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
     * @param   string  $default   default value to fall back to
     * @return  string
     * @since   2.0.0
     */
    public function ifIsFile($basePath = null, $default = null)
    {
        $path = ((null != $basePath) ? ($basePath . '/') : (''));
        return $this->withValidator(new validator\FileValidator($basePath),
                                    'FILE_INVALID',
                                    ['PATH' => $path . $this->param->value()],
                                    $default
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
     * @param   string  $default   default value to fall back to
     * @return  string
     * @since   2.0.0
     */
    public function ifIsDirectory($basePath = null, $default = null)
    {
        $path = ((null != $basePath) ? ($basePath . '/') : (''));
        return $this->withValidator(new validator\DirectoryValidator($basePath),
                                    'DIRECTORY_INVALID',
                                    ['PATH' => $path . $this->param->value()],
                                    $default
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
     * @param   string     $default    optional  default value to fall back to
     * @return  string
     */
    public function withValidator(Validator $validator, $errorId, array $details = [], $default = null)
    {
        return $this->handleFilter(function() use($validator, $errorId, $details)
                                   {
                                       return new filter\ValidatingFilter($validator, $errorId, $details);
                                   },
                                   $default
        );
    }

    /**
     * handles a filter
     *
     * @param   \Closure  $createFilter
     * @param   mixed     $default       optional  default value to fall back to
     * @return  mixed
     */
    private function handleFilter(\Closure $createFilter, $default = null)
    {
        if ($this->param->isNull()) {
            if ($this->required) {
                $this->paramErrors->append($this->param->name(), $this->requiredErrorId);
                return null;
            }

            return $default;
        }

        return $this->applyFilter($createFilter());
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
        if ($this->required && $this->param->isEmpty()) {
            $this->paramErrors->append($this->param->name(), $this->requiredErrorId);
            return null;
        }

        return $this->applyFilter($filter);
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
     * @return  bool
     */
    public function withFunction(\Closure $filter)
    {
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
