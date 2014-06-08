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
use stubbles\input\Filter;
use stubbles\input\Validator;
use stubbles\input\filter\ArrayFilter;
use stubbles\input\filter\PasswordFilter;
use stubbles\input\filter\range\DateRange;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\filter\range\StringLength;
use stubbles\input\filter\range\NumberRange;
/**
 * Marker interface for all ValueReader methods which support a default value.
 *
 * @since  3.0.0
 */
class MissingValueReader implements NoDefaultValueReader
{
    /**
     * request instance the value inherits from
     *
     * @type  \Closure
     */
    private $reportError;
    /**
     * error id to be used if param is required but empty
     *
     * @type  string
     */
    private $defaultErrorId;

    /**
     * constructor
     *
     * @param  \Closure  $reportError
     * @param  string    $defaultErrorId
     */
    public function __construct(\Closure $reportError, $defaultErrorId)
    {
        $this->reportError    = $reportError;
        $this->defaultErrorId = $defaultErrorId;
    }

    /**
     * reports the error
     *
     * @param  string  $errorId  optional
     */
    private function reportError($errorId = null)
    {
        $reportError = $this->reportError;
        $reportError((null === $errorId) ? ($this->defaultErrorId) : ($errorId));
    }

    /**
     * read as array value
     *
     * @param   string  $separator  optional  character to split input value with
     * @return  array
     */
    public function asArray($separator = ArrayFilter::SEPARATOR_DEFAULT)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as boolean value
     *
     * @return  bool
     */
    public function asBool()
    {
        $this->reportError();
        return null;
    }

    /**
     * read as integer value
     *
     * @param   NumberRange  $range
     * @return  int
     */
    public function asInt(NumberRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as float value
     *
     * @param   NumberRange  $range
     * @param   int          $decimals  number of decimals
     * @return  float
     */
    public function asFloat(NumberRange $range = null, $decimals = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as string value
     *
     * @param   StringLength  $length
     * @return  string
     */
    public function asString(StringLength $length = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as string value
     *
     * @param   StringLength  $length
     * @return  \stubbles\lang\SecureString
     */
    public function asSecureString(StringLength $length = null)
    {
        $this->reportError();
        return null;
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
        $this->reportError();
        return null;
    }

    /**
     * read as json value
     *
     * @return  \stdClass|array
     */
    public function asJson()
    {
        $this->reportError();
        return null;
    }

    /**
     * read as password value
     *
     * @param   int       $minDiffChars      minimum amount of different characters within password
     * @param   string[]  $nonAllowedValues  list of values that are not allowed as password
     * @return  \stubbles\lang\SecureString
     */
    public function asPassword($minDiffChars = PasswordFilter::MIN_DIFF_CHARS_DEFAULT, array $nonAllowedValues = [])
    {
        $this->reportError();
        return null;
    }

    /**
     * read as http uri
     *
     * @return  HttpUri
     */
    public function asHttpUri()
    {
        $this->reportError(('FIELD_EMPTY' === $this->defaultErrorId) ? ('HTTP_URI_MISSING') : ($this->defaultErrorId));
        return null;
    }

    /**
     * read as http uri if it does exist
     *
     * @return  HttpUri
     */
    public function asExistingHttpUri()
    {
        $this->reportError(('FIELD_EMPTY' === $this->defaultErrorId) ? ('HTTP_URI_MISSING') : ($this->defaultErrorId));
        return null;
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @return  string
     */
    public function asMailAddress()
    {
        $this->reportError(('FIELD_EMPTY' === $this->defaultErrorId) ? ('MAILADDRESS_MISSING') : ($this->defaultErrorId));
        return null;
    }

    /**
     * read as date value
     *
     * @param   DateRange                  $range
     * @return  \stubbles\date\Date
     */
    public function asDate(DateRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as day
     *
     * @param   DatespanRange  $range
     * @return  \stubbles\date\span\Day
     */
    public function asDay(DatespanRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as month
     *
     * @param   DatespanRange  $range
     * @return  \stubbles\date\span\Month
     */
    public function asMonth(DatespanRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @return  string
     */
    public function ifIsIpAddress()
    {
        $this->reportError();
        return null;
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param   string[]  $allowedValues  list of allowed values
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues)
    {
        $this->reportError();
        return null;
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @param   string  $regex    regular expression to apply
     * @return  string
     */
    public function ifSatisfiesRegex($regex)
    {
        $this->reportError();
        return null;
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
        $this->reportError();
        return null;
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
        $this->reportError();
        return null;
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
     */
    public function withValidator(Validator $validator, $errorId, array $details = [])
    {
        $this->reportError();
        return null;
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
        $this->reportError();
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
     * @param   callable  $filter
     * @return  mixed
     */
    public function withCallable(callable $filter)
    {
        $this->reportError();
        return null;
    }
}
