<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\valuereader;
use stubbles\input\Filter;
use stubbles\input\filter\JsonFilter;
use stubbles\input\filter\PasswordChecker;
use stubbles\input\filter\range\DateRange;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\filter\range\StringLength;
use stubbles\input\filter\range\NumberRange;
use stubbles\values\Secret;
/**
 * Marker interface for all ValueReader methods which support a default value.
 *
 * @since  3.0.0
 */
class MissingValueReader implements CommonValueReader
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
    public function __construct(\Closure $reportError, string $defaultErrorId)
    {
        $this->reportError    = $reportError;
        $this->defaultErrorId = $defaultErrorId;
    }

    /**
     * reports the error
     *
     * @param  string  $errorId  optional
     */
    private function reportError(string $errorId = null)
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
    public function asArray(string $separator = self::ARRAY_SEPARATOR): ?array
    {
        $this->reportError();
        return null;
    }

    /**
     * read as boolean value
     *
     * @return  bool
     */
    public function asBool(): ?bool
    {
        $this->reportError();
        return null;
    }

    /**
     * read as integer value
     *
     * @param   \stubbles\input\filter\range\NumberRange  $range
     * @return  int
     */
    public function asInt(NumberRange $range = null): ?int
    {
        $this->reportError();
        return null;
    }

    /**
     * read as float value
     *
     * @param   \stubbles\input\filter\range\NumberRange  $range
     * @param   int          $decimals  number of decimals
     * @return  float
     */
    public function asFloat(NumberRange $range = null, int $decimals = null): ?float
    {
        $this->reportError();
        return null;
    }

    /**
     * read as string value
     *
     * @param   \stubbles\input\filter\range\StringLength  $length
     * @return  string
     */
    public function asString(StringLength $length = null): ?string
    {
        $this->reportError();
        return null;
    }

    /**
     * read as secret
     *
     * @param   \stubbles\input\filter\range\StringLength  $length
     * @return  \stubbles\values\Secret
     */
    public function asSecret(StringLength $length = null): ?Secret
    {
        $this->reportError();
        return null;
    }

    /**
     * read as text value
     *
     * @param   \stubbles\input\filter\range\StringLength  $length
     * @param   string[]                                   $allowedTags  list of allowed tags
     * @return  string
     */
    public function asText(StringLength $length = null, array $allowedTags = []): ?string
    {
        $this->reportError();
        return null;
    }

    /**
     * read as json value
     *
     * @param   int  $maxLength  maximum allowed length of incoming JSON document in bytes  optional
     * @return  \stdClass|array|null
     */
    public function asJson(int $maxLength = JsonFilter::DEFAULT_MAX_LENGTH)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as password value
     *
     * @param   \stubbles\input\filter\PasswordChecker  $checker  checker to be used to ensure a good password
     * @return  \stubbles\values\Secret
     */
    public function asPassword(PasswordChecker $checker): ?Secret
    {
        $this->reportError();
        return null;
    }

    /**
     * read as http uri
     *
     * @return  \stubbles\peer\http\HttpUri|null
     */
    public function asHttpUri()
    {
        $this->reportError(('FIELD_EMPTY' === $this->defaultErrorId) ? ('HTTP_URI_MISSING') : ($this->defaultErrorId));
        return null;
    }

    /**
     * read as http uri if it does exist
     *
     * @return  \stubbles\peer\http\HttpUri|null
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
    public function asMailAddress(): ?string
    {
        $this->reportError(('FIELD_EMPTY' === $this->defaultErrorId) ? ('MAILADDRESS_MISSING') : ($this->defaultErrorId));
        return null;
    }

    /**
     * read as date value
     *
     * @param   \stubbles\input\filter\range\DateRange  $range
     * @return  \stubbles\date\Date|null
     */
    public function asDate(DateRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as day
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Day|null
     */
    public function asDay(DatespanRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as week
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Week|null
     * @since   4.5.0
     */
    public function asWeek(DatespanRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as month
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Month|null
     */
    public function asMonth(DatespanRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * read as datespan
     *
     * In case the default value is not of type stubbles\date\span\Datespan an
     * IllegalStateException will be thrown.
     *
     * @param   \stubbles\input\filter\range\DatespanRange  $range
     * @return  \stubbles\date\span\Datespan|null
     * @since   4.3.0
     */
    public function asDatespan(DatespanRange $range = null)
    {
        $this->reportError();
        return null;
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @return  string
     */
    public function ifIsIpAddress(): ?string
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
    public function ifIsOneOf(array $allowedValues): ?string
    {
        $this->reportError();
        return null;
    }

    /**
     * returns value if it is matched by given regular expression
     *
     * @param   string  $regex  regular expression to apply
     * @return  string
     * @since   6.0.0
     */
    public function ifMatches(string $regex): ?string
    {
        $this->reportError();
        return null;
    }

    /**
     * returns param value when given predicate evaluates to true
     *
     * If value does not satisfy the predicate return value will be null.
     *
     * @api
     * @param   callable  $predicate  predicate to use
     * @param   string    $errorId    error id to be used in case validation fails
     * @param   array     $details    optional  details for param error in case validation fails
     * @return  string
     * @since   3.0.0
     */
    public function when(callable $predicate, string $errorId, array $details = []): ?string
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
     * @param   \stubbles\input\Filter  $filter
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
