<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\Param;
use net\stubbles\input\ParamError;
use net\stubbles\input\ParamErrors;
use net\stubbles\input\filter\expectation\DateExpectation;
use net\stubbles\input\filter\expectation\StringExpectation;
use net\stubbles\input\filter\expectation\NumberExpectation;
use net\stubbles\input\filter\expectation\ValueExpectation;
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
        $this->param       = $param;
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

        return $this->applyFilter(new BoolFilter());
    }

    /**
     * read as integer value
     *
     * @api
     * @param   NumberExpectation  $expect
     * @return  int
     */
    public function asInt(NumberExpectation $expect = null)
    {
        return $this->handleFilter(function() use($expect)
                                   {
                                       return RangeFilter::wrap(new IntegerFilter(),
                                                                $expect
                                       );
                                   },
                                   $expect
        );
    }

    /**
     * read as float value
     *
     * @api
     * @param   NumberExpectation  $expect
     * @param   int                $decimals  number of decimals
     * @return  float
     */
    public function asFloat(NumberExpectation $expect = null, $decimals = null)
    {
        return $this->handleFilter(function() use($expect, $decimals)
                                   {
                                       $floatFilter = new FloatFilter();
                                       return RangeFilter::wrap($floatFilter->setDecimals($decimals),
                                                                $expect
                                       );
                                   },
                                   $expect
        );
    }

    /**
     * read as string value
     *
     * @api
     * @param   StringExpectation  $expect
     * @return  string
     */
    public function asString(StringExpectation $expect = null)
    {
        return $this->handleFilter(function() use($expect)
                                   {
                                       return RangeFilter::wrap(new StringFilter(),
                                                                $expect
                                       );
                                   },
                                   $expect
        );
    }

    /**
     * read as text value
     *
     * @api
     * @param   StringExpectation  $expect
     * @param   string[]           $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText(StringExpectation $expect = null, $allowedTags = array())
    {
        return $this->handleFilter(function() use($expect, $allowedTags)
                                   {
                                       $textFilter = new TextFilter();
                                       return RangeFilter::wrap($textFilter->allowTags($allowedTags),
                                                                $expect
                                       );
                                   },
                                   $expect
        );
    }

    /**
     * read as json value
     *
     * @api
     * @param   ValueExpectation  $expect
     * @return  \stdClass|array
     */
    public function asJson(ValueExpectation $expect = null)
    {
        return $this->handleFilter(function()
                                   {
                                       return new JsonFilter();
                                   },
                                   $expect
        );
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
        $passWordFilter = new PasswordFilter();
        return $this->withFilter($passWordFilter->minDiffChars($minDiffChars)
                                                ->disallowValues($nonAllowedValues),
                                 $required
        );
    }

    /**
     * read as http uri
     *
     * @api
     * @param   ValueExpectation  $expect
     * @return  HttpUri
     */
    public function asHttpUri(ValueExpectation $expect = null)
    {
        return $this->handleFilter(function()
                                   {
                                       return new HttpUriFilter();
                                   },
                                   $expect
        );
    }

    /**
     * read as http uri if it does exist
     *
     * @api
     * @param   ValueExpectation  $expect
     * @return  HttpUri
     */
    public function asExistingHttpUri(ValueExpectation $expect = null)
    {
        return $this->handleFilter(function()
                                   {
                                       $httpUriFilter = new HttpUriFilter();
                                       return $httpUriFilter->enforceDnsRecord();
                                   },
                                   $expect
        );
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
        return $this->withFilter(new MailFilter(), $required);
    }

    /**
     * read as date value
     *
     * @api
     * @param   DateExpectation  $expect
     * @return  Date

     */
    public function asDate(DateExpectation $expect = null)
    {
        return $this->handleFilter(function() use($expect)
                                   {
                                       return RangeFilter::wrap(new DateFilter(),
                                                                $expect
                                       );
                                   },
                                   $expect
        );
    }

    /**
     * handles a filter
     *
     * @param   \Closure          $createFilter
     * @param   ValueExpectation  $expect
     * @return  mixed
     */
    private function handleFilter(\Closure $createFilter, ValueExpectation $expect = null)
    {
        if (null !== $expect) {
            if (!$expect->isSatisfied($this->param)) {
                $this->paramErrors->add(new ParamError('FIELD_EMPTY'), $this->param->getName());
                return null;
            }

            if ($expect->allowsDefault($this->param)) {
                return $expect->getDefault();
            }
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
     * @param   bool    $required  if a value is required, defaults to false
     * @return  mixed
     */
    public function withFilter(Filter $filter, $required = false)
    {
        if (true === $required && $this->param->isEmpty()) {
            $this->paramErrors->add(new ParamError('FIELD_EMPTY'), $this->param->getName());
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

        foreach ($this->param->getErrors() as $error) {
            $this->paramErrors->add($error, $this->param->getName());
        }

        return null;
    }
}
?>