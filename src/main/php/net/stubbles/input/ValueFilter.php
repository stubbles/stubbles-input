<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input;
use net\stubbles\input\error\ParamError;
use net\stubbles\input\error\ParamErrors;
use net\stubbles\input\filter\Filter;
use net\stubbles\input\validator\Validator;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\types\Date;
use net\stubbles\peer\http\HttpUri;
/**
 * Value object for request values to filter them or retrieve them after validation.
 *
 * @since  1.3.0
 */
class ValueFilter extends BaseObject
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
        $this->param        = $param;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string  $paramName
     * @param   string  $paramValue
     * @return  ValueFilter
     */
    public static function createAsMock($paramName, $paramValue)
    {
        return new self(new ParamErrors(), new Param($paramName, $paramValue));
    }

    /**
     * read as boolean value
     *
     * @param   bool  $default  default value to fall back to
     * @return  bool
     * @since   1.7.0
     */
    public function asBool($default = null)
    {
        if ($this->param->isNull() && null !== $default) {
            return $default;
        }

        return $this->withFilter(new \net\stubbles\input\filter\BoolFilter());
    }

    /**
     * read as integer value
     *
     * @param   int   $min       minimum allowed value
     * @param   int   $max       maximum allowed value
     * @param   int   $default   default value to fall back to
     * @param   bool  $required  if a value is required, defaults to false
     * @return  int
     */
    public function asInt($min = null, $max = null, $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter(new \net\stubbles\input\filter\RangeFilter(new \net\stubbles\input\filter\IntegerFilter(),
                                                                            $min,
                                                                            $max
                                 ),
                                 $required
        );
    }

    /**
     * read as float value
     *
     * @param   int    $min       minimum allowed value
     * @param   int    $max       maximum allowed value
     * @param   float  $default   default value to fall back to
     * @param   bool   $required  if a value is required, defaults to false
     * @param   int    $decimals  number of decimals
     * @return  float
     */
    public function asFloat($min = null, $max = null, $default = null, $required = false, $decimals = null)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        $floatFilter = new \net\stubbles\input\filter\FloatFilter();
        return $this->withFilter(new \net\stubbles\input\filter\RangeFilter($floatFilter->setDecimals($decimals),
                                                                            $min,
                                                                            $max
                                 ),
                                 $required
        );
    }

    /**
     * read as string value
     *
     * @param   int     $minLength  minimum length of string
     * @param   int     $maxLength  maximum length of string
     * @param   string  $default    default value to fall back to
     * @param   bool    $required   if a value is required, defaults to false
     * @return  string
     */
    public function asString($minLength = null, $maxLength = null, $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter(new \net\stubbles\input\filter\LengthFilter(new \net\stubbles\input\filter\StringFilter(),
                                                                             $minLength,
                                                                             $maxLength
                                 ),
                                 $required
        );
    }

    /**
     * read as text value
     *
     * @param   int       $minLength    minimum length of string
     * @param   int       $maxLength    maximum length of string
     * @param   string    $default      default value to fall back to
     * @param   bool      $required     if a value is required, defaults to false
     * @param   string[]  $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText($minLength = null, $maxLength = null, $default = null, $required = false, $allowedTags = array())
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        $textFilter = new \net\stubbles\input\filter\TextFilter();
        return $this->withFilter(new \net\stubbles\input\filter\LengthFilter($textFilter->allowTags($allowedTags),
                                                                             $minLength,
                                                                             $maxLength
                                 ),
                                 $required
        );
    }

    /**
     * read as json value
     *
     * @param   string  $default   default value to fall back to
     * @param   bool    $required  if a value is required, defaults to false
     * @return  string
     */
    public function asJson($default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter(new \net\stubbles\input\filter\JsonFilter(), $required);
    }

    /**
     * read as password value
     *
     * @param   int       $minDiffChars      minimum amount of different characters within password
     * @param   string[]  $nonAllowedValues  list of values that are not allowed as password
     * @param   bool      $required          if a value is required, defaults to true
     * @return  string
     */
    public function asPassword($minDiffChars = 5, array $nonAllowedValues = array(), $required = true)
    {
        $passWordFilter = new \net\stubbles\input\filter\PasswordFilter();
        return $this->withFilter($passWordFilter->minDiffChars($minDiffChars)
                                                ->disallowValues($nonAllowedValues),
                                 $required
        );
    }

    /**
     * read as http url
     *
     * @param   bool     $checkDns  whether url should be checked via DNS
     * @param   HttpUri  $default   default value to fall back to
     * @param   bool     $required  if a value is required, defaults to false
     * @return  HttpUri
     */
    public function asHttpUri($checkDns = false, HttpUri $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        $httpUriFilter = new \net\stubbles\input\filter\HttpUriFilter();
        if (true === $checkDns) {
            $httpUriFilter->enforceDnsRecord();
        }

        return $this->withFilter($httpUriFilter, $required);
    }

    /**
     * read as mail address
     *
     * @param   bool  $required  if a value is required, defaults to false
     * @return  string
     */
    public function asMailAddress($required = false)
    {
        return $this->withFilter(new \net\stubbles\input\filter\MailFilter(), $required);
    }

    /**
     * read as date value
     *
     * @param   Date  $minDate    smallest allowed date
     * @param   Date  $maxDate    greatest allowed date
     * @param   Date  $default    default value to fall back to
     * @param   bool  $required   if a value is required, defaults to false
     * @return  Date

     */
    public function asDate(Date $minDate = null, Date $maxDate = null, Date $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter(new \net\stubbles\input\filter\PeriodFilter(new \net\stubbles\input\filter\DateFilter(),
                                                                             $minDate,
                                                                             $maxDate
                                 ),
                                 $required
        );
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * @param   Filter  $filter
     * @param   mixed   $default  default value to fall back to
     * @param   bool    $required  if a value is required, defaults to false
     * @return  mixed
     */
    public function withFilter(Filter $filter, $required = false)
    {
        if (true === $required && $this->param->isEmpty()) {
            $this->paramErrors->add(new ParamError('FIELD_EMPTY'), $this->param->getName());
            return null;
        }

        $value = $filter->apply($this->param);
        if (!$this->param->hasErrors()) {
            return $value;
        }

        foreach ($this->param->getErrors() as $error) {
            $this->paramErrors->add($error, $this->param->getName());
        }

        return null;
    }

    /**
     * returns value if it contains given string, and null otherwise
     *
     * @param   string  $contained  byte sequence the value must contain
     * @param   string  $default    default value to fall back to
     * @return  string
     */
    public function ifContains($contained, $default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\ContainsValidator($contained), $default);
    }

    /**
     * returns value if it eqals an expected value, and null otherwise
     *
     * @param   string  $expected  byte sequence the value must be equal to
     * @param   string  $default   default value to fall back to
     * @return  bool
     */
    public function ifIsEqualTo($expected, $default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\EqualValidator($expected), $default);
    }

    /**
     * returns value if it is an http url, and null otherwise
     *
     * @param   bool    $checkDns  whether to verify url via DNS
     * @param   string  $default   default value to fall back to
     * @return  string
     */
    public function ifIsHttpUri($checkDns = false, $default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\HttpUriValidator($checkDns), $default);
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifIsIpAddress($default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\IpValidator(), $default);
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifIsMailAddress($default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\MailValidator(), $default);
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param   array<string>  $allowedValues  list of allowed values
     * @param   string         $default        default value to fall back to
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues, $default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\PreSelectValidator($allowedValues), $default);
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @param   string  $regex    regular expression to apply
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifSatisfiesRegex($regex, $default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\RegexValidator($regex), $default);
    }

    /**
     * checks value with given validator
     *
     * If value does not satisfy the validator return value will be null.
     *
     * @param   Validator  $validator  validator to use
     * @param   string     $default    default value to fall back to
     * @return  string
     */
    public function withValidator(Validator $validator, $default = null)
    {
        if ($this->useDefault()) {
            return $default;
        }

        if ($validator->validate($this->param->getValue())) {
            return $this->param->getValue();
        }

        return $default;
    }

    /**
     * returns value unvalidated
     *
     * This should be used with greatest care.
     *
     * @return  string
     */
    public function unsecure()
    {
        return $this->param->getValue();
    }

    /**
     * checks whether default value should be used
     *
     * @param   bool  $required
     * @return  bool
     */
    private function useDefault($required = false)
    {
        return ($this->param->isNull() && false === $required);
    }
}
?>