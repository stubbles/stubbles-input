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
use net\stubbles\input\filter\range\DateRange;
use net\stubbles\input\filter\range\LengthRange;
use net\stubbles\input\filter\range\NumberRange;
use net\stubbles\input\filter\range\Range;
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

        return $this->withFilter(new \net\stubbles\input\filter\BoolFilter());
    }

    /**
     * read as integer value
     *
     * @api
     * @param   NumberRange  $range     range where value has to be inside
     * @param   int          $default   default value to fall back to
     * @param   bool         $required  if a value is required, defaults to false
     * @return  int
     */
    public function asInt(NumberRange $range = null, $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter($this->wrapRange(new \net\stubbles\input\filter\IntegerFilter(),
                                                  $range
                                 ),
                                 $required
        );
    }

    /**
     * read as float value
     *
     * @api
     * @param   NumberRange  $range     range where value has to be inside
     * @param   float        $default   default value to fall back to
     * @param   bool         $required  if a value is required, defaults to false
     * @param   int          $decimals  number of decimals
     * @return  float
     */
    public function asFloat(NumberRange $range = null, $default = null, $required = false, $decimals = null)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        $floatFilter = new \net\stubbles\input\filter\FloatFilter();
        return $this->withFilter($this->wrapRange($floatFilter->setDecimals($decimals),
                                                  $range
                                 ),
                                 $required
        );
    }

    /**
     * read as string value
     *
     * @api
     * @param   LengthRange  $range     length definition for string
     * @param   string       $default   default value to fall back to
     * @param   bool         $required  if a value is required, defaults to false
     * @return  string
     */
    public function asString(LengthRange $range = null, $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter($this->wrapRange(new \net\stubbles\input\filter\StringFilter(),
                                                  $range
                                 ),
                                 $required
        );
    }

    /**
     * read as text value
     *
     * @api
     * @param   LengthRange  $range        length definition for text
     * @param   string       $default      default value to fall back to
     * @param   bool         $required     if a value is required, defaults to false
     * @param   string[]     $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText(LengthRange $range = null, $default = null, $required = false, $allowedTags = array())
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        $textFilter = new \net\stubbles\input\filter\TextFilter();
        return $this->withFilter($this->wrapRange($textFilter->allowTags($allowedTags),
                                                  $range
                                 ),
                                 $required
        );
    }

    /**
     * read as json value
     *
     * @api
     * @param   string  $default   default value to fall back to
     * @param   bool    $required  if a value is required, defaults to false
     * @return  \stdClass|array
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
     * @api
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
     * read as http uri
     *
     * @api
     * @param   HttpUri  $default   default value to fall back to
     * @param   bool     $required  if a value is required, defaults to false
     * @return  HttpUri
     */
    public function asHttpUri(HttpUri $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter(new \net\stubbles\input\filter\HttpUriFilter(), $required);
    }

    /**
     * read as http uri if it does exist
     *
     * @api
     * @param   HttpUri  $default   default value to fall back to
     * @param   bool     $required  if a value is required, defaults to false
     * @return  HttpUri
     */
    public function asExistingHttpUri(HttpUri $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        $httpUriFilter = new \net\stubbles\input\filter\HttpUriFilter();
        return $this->withFilter($httpUriFilter->enforceDnsRecord(), $required);
    }

    /**
     * read as mail address
     *
     * @api
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
     * @api
     * @param   DateRange  $range     validity range for date
     * @param   Date       $default   default value to fall back to
     * @param   bool       $required  if a value is required, defaults to false
     * @return  Date

     */
    public function asDate(DateRange $range = null, Date $default = null, $required = false)
    {
        if ($this->useDefault($required)) {
            return $default;
        }

        return $this->withFilter($this->wrapRange(new \net\stubbles\input\filter\DateFilter(),
                                                  $range
                                 ),
                                 $required
        );
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * @api
     * @param   Filter  $filter
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
     * @api
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
     * @api
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
     * @api
     * @param   string  $default   default value to fall back to
     * @return  string
     */
    public function ifIsHttpUri($default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\HttpUriValidator(), $default);
    }

    /**
     * returns value if it is an http url, and null otherwise
     *
     * @api
     * @param   string  $default   default value to fall back to
     * @return  string
     */
    public function ifIsExistingHttpUri($default = null)
    {
        $httpUriValidator = new \net\stubbles\input\validator\HttpUriValidator();
        return $this->withValidator($httpUriValidator->enableDnsCheck(), $default);
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
        return $this->withValidator(new \net\stubbles\input\validator\IpValidator(), $default);
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @api
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
     * @api
     * @param   string[]  $allowedValues  list of allowed values
     * @param   string    $default        default value to fall back to
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues, $default = null)
    {
        return $this->withValidator(new \net\stubbles\input\validator\PreSelectValidator($allowedValues), $default);
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
        return $this->withValidator(new \net\stubbles\input\validator\RegexValidator($regex), $default);
    }

    /**
     * checks value with given validator
     *
     * If value does not satisfy the validator return value will be null.
     *
     * @api
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
     * @api
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

    /**
     * wraps filter into range filter if range given
     *
     * @param   Filter  $filter  filter to wrap
     * @param   Range   $range   range to be used
     * @return  Filter
     */
    private function wrapRange(Filter $filter, Range $range = null)
    {
        if (null === $range) {
            return $filter;
        }

        return new \net\stubbles\input\filter\RangeFilter($filter, $range);
    }
}
?>