<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\valuereader;
use stubbles\date\Date;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\input\Filter;
use stubbles\input\Validator;
use stubbles\input\filter\ArrayFilter;
use stubbles\input\filter\PasswordChecker;
use stubbles\input\filter\range\DateRange;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\filter\range\StringLength;
use stubbles\input\filter\range\NumberRange;
use stubbles\lang\SecureString;
use stubbles\lang\exception\IllegalStateException;
use stubbles\lang\exception\MethodNotSupportedException;
use stubbles\peer\http\HttpUri;
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
     * @throws  IllegalStateException
     */
    private function checkDefaultType(\Closure $isCorrectType, $expectedType)
    {
        if (!$isCorrectType()) {
            throw new IllegalStateException('Default value is not of type ' . $expectedType . ' but of type ' . \stubbles\lang\getType($this->default));
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
    public function asArray($separator = ArrayFilter::SEPARATOR_DEFAULT)
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
    public function asBool()
    {
        return (bool) $this->default;
    }

    /**
     * read as integer value
     *
     * In case the default value is not of type int an IllegalStateException
     * will be thrown.
     *
     * @param   NumberRange  $range
     * @return  int
     */
    public function asInt(NumberRange $range = null)
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
     * @param   NumberRange  $range
     * @param   int          $decimals  number of decimals
     * @return  float
     */
    public function asFloat(NumberRange $range = null, $decimals = null)
    {
        $this->checkDefaultType(function() { return is_float($this->default);}, 'float');
        return $this->default;
    }

    /**
     * read as string value
     *
     * @param   StringLength  $length
     * @return  string
     */
    public function asString(StringLength $length = null)
    {
        return $this->default;
    }

    /**
     * read as string value
     *
     * @param   StringLength  $length
     * @return  \stubbles\lang\SecureString
     */
    public function asSecureString(StringLength $length = null)
    {
        $this->checkDefaultType(function() { return ($this->default instanceof SecureString); }, 'stubbles\lang\SecureString');
        return $this->default;
    }

    /**
     * read as text value
     *
     * @param   StringLength  $length
     * @param   string[]      $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText(StringLength $length = null, $allowedTags = [])
    {
        return $this->default;
    }

    /**
     * read as json value
     *
     * @return  \stdClass|array
     */
    public function asJson()
    {
        return $this->default;
    }

    /**
     * read as password value
     *
     * Default values for passwords make no sense, therefor all calls to this
     * method trigger a MethodNotSupportedException.
     *
     * @param   PasswordChecker  $checker  checker to be used to ensure a good password
     * @return  \stubbles\lang\SecureString
     * @throws  MethodNotSupportedException
     */
    public function asPassword(PasswordChecker $checker)
    {
        throw new MethodNotSupportedException('Default passwords are not supported');
    }

    /**
     * read as http uri
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * IllegalStateException will be thrown.
     *
     * @return  \stubbles\peer\http\HttpUri
     */
    public function asHttpUri()
    {
        $this->checkDefaultType(function() { return ($this->default instanceof HttpUri); }, 'stubbles\peer\http\HttpUri');
        return $this->default;
    }

    /**
     * read as http uri if it does exist
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * IllegalStateException will be thrown.
     *
     * @return  \stubbles\peer\http\HttpUri
     */
    public function asExistingHttpUri()
    {
        $this->checkDefaultType(function() { return ($this->default instanceof HttpUri); }, 'stubbles\peer\http\HttpUri');
        return $this->default;
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @return  string
     */
    public function asMailAddress()
    {
        return $this->default;
    }

    /**
     * read as date value
     *
     * In case the default value is not of type stubbles\date\Date an IllegalStateException
     * will be thrown.
     *
     * @param   DateRange  $range
     * @return  \stubbles\date\Date
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
     * @param   DatespanRange  $range
     * @return  \stubbles\date\span\Day
     */
    public function asDay(DatespanRange $range = null)
    {
        $this->checkDefaultType(function() { return $this->default instanceof Day;}, 'stubbles\date\span\Day');
        return $this->default;
    }

    /**
     * read as month
     *
     * In case the default value is not of type stubbles\date\span\Month an
     * IllegalStateException will be thrown.
     *
     * @param   DatespanRange  $range
     * @return  \stubbles\date\span\Month
     */
    public function asMonth(DatespanRange $range = null)
    {
        $this->checkDefaultType(function() { return $this->default instanceof Month;}, 'stubbles\date\span\Month');
        return $this->default;
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @return  string
     */
    public function ifIsIpAddress()
    {
        return $this->default;
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param   string[]  $allowedValues  list of allowed values
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues)
    {
        return $this->default;
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @param   string  $regex    regular expression to apply
     * @return  string
     */
    public function ifSatisfiesRegex($regex)
    {
        return $this->default;
    }

    /**
     * returns value if it denotes a path to an existing file, and null otherwise
     *
     * This should be used with greatest care in web environments as it only
     * checks if the file exists, but not if there are any rights to access
     * the specific file. It also does not prevent constructions which would
     * allow an attacker to reach e.g. /etc/passwd via ../../ constructions.
     *
     * @param   string  $basePath  base path where file must reside in
     * @return  string
     */
    public function ifIsFile($basePath = null)
    {
        return $this->default;
    }

    /**
     * returns value if it denotes a path to an existing directory, and null otherwise
     *
     * This should be used with greatest care in web environments as it only
     * checks if the directory exists, but not if there are any rights to access
     * the specific directory. It also does not prevent constructions which would
     * allow an attacker to reach a certain directory via ../../ constructions.
     *
     * @param   string  $basePath  base path where directory must reside in
     * @return  string
     */
    public function ifIsDirectory($basePath = null)
    {
        return $this->default;
    }

    /**
     * checks value with given validator
     *
     * If value does not satisfy the validator return value will be null.
     *
     * @param   Validator  $validator  validator to use
     * @param   string     $errorId    error id to be used in case validation fails
     * @param   array      $details    optional  details for param error in case validation fails
     * @return  string
     * @deprecated  since 3.0.0, use with($predicate, $errorId) instead, will be removed with 4.0.0
     */
    public function withValidator(Validator $validator, $errorId, array $details = [])
    {
        return $this->default;
    }

    /**
     * returns param value when given predicate evaluates to true
     *
     * If value does not satisfy the predicate return value will be null.
     *
     * @api
     * @param   \stubbles\predicate\Predicate|callable  $predicate  predicate to use
     * @param   string                                  $errorId    error id to be used in case validation fails
     * @param   array                                   $details    optional  details for param error in case validation fails
     * @return  string
     * @since   3.0.0
     */
    public function when($predicate, $errorId, array $details = [])
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
     * @param   Filter  $filter
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
