<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\valuereader;

use Closure;
use stdClass;
use stubbles\date\Date;
use stubbles\date\span\Datespan;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\date\span\Week;
use stubbles\input\Filter;
use stubbles\input\filter\JsonFilter;
use stubbles\input\filter\PasswordChecker;
use stubbles\input\filter\range\DateRange;
use stubbles\input\filter\range\DatespanRange;
use stubbles\input\filter\range\SecretMinLength;
use stubbles\input\filter\range\StringLength;
use stubbles\input\filter\range\NumberRange;
use stubbles\peer\http\HttpUri;
use stubbles\values\Secret;
/**
 * Marker interface for all ValueReader methods which support a default value.
 *
 * @since  3.0.0
 */
class MissingValueReader implements CommonValueReader
{
    public function __construct(
        private Closure $reportError,
        private string $defaultErrorId
    ) { }

    /**
     * reports the error
     *
     * @param  string  $errorId  optional
     */
    private function reportError(?string $errorId = null): void
    {
        $reportError = $this->reportError;
        $reportError($errorId ?? $this->defaultErrorId);
    }

    /**
     * read as array value
     *
     * @param   string  $separator  optional  character to split input value with
     * @return  mixed[]
     */
    public function asArray(string $separator = self::ARRAY_SEPARATOR): ?array
    {
        $this->reportError();
        return null;
    }

    /**
     * read as boolean value
     */
    public function asBool(): ?bool
    {
        $this->reportError();
        return null;
    }

    /**
     * read as integer value
     */
    public function asInt(?NumberRange $range = null): ?int
    {
        $this->reportError();
        return null;
    }

    /**
     * read as float value
     */
    public function asFloat(?NumberRange $range = null, ?int $decimals = null): ?float
    {
        $this->reportError();
        return null;
    }

    /**
     * read as string value
     */
    public function asString(?StringLength $length = null): ?string
    {
        $this->reportError();
        return null;
    }

    /**
     * read as secret
     */
    public function asSecret(?SecretMinLength $length = null): ?Secret
    {
        $this->reportError();
        return null;
    }

    /**
     * read as text value
     *
     * @param   string[]  $allowedTags  list of allowed tags
     */
    public function asText(?StringLength $length = null, array $allowedTags = []): ?string
    {
        $this->reportError();
        return null;
    }

    /**
     * read as json value
     *
     * @param   int  $maxLength  maximum allowed length of incoming JSON document in bytes  optional
     */
    public function asJson(int $maxLength = JsonFilter::DEFAULT_MAX_LENGTH): stdClass|array|null
    {
        $this->reportError();
        return null;
    }

    /**
     * read as password value
     */
    public function asPassword(PasswordChecker $checker): ?Secret
    {
        $this->reportError();
        return null;
    }

    /**
     * read as http uri
     */
    public function asHttpUri(): ?HttpUri
    {
        $this->reportError(
            'FIELD_EMPTY' === $this->defaultErrorId ? 'HTTP_URI_MISSING' : $this->defaultErrorId
        );
        return null;
    }

    /**
     * read as http uri if it does exist
     */
    public function asExistingHttpUri(): ?HttpUri
    {
        $this->reportError(
            'FIELD_EMPTY' === $this->defaultErrorId ? 'HTTP_URI_MISSING' : $this->defaultErrorId
        );
        return null;
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @return  string
     */
    public function asMailAddress(): ?string
    {
        $this->reportError(
            'FIELD_EMPTY' === $this->defaultErrorId ? 'MAILADDRESS_MISSING' : $this->defaultErrorId
        );
        return null;
    }

    /**
     * read as date value
     */
    public function asDate(?DateRange $range = null): ?Date
    {
        $this->reportError();
        return null;
    }

    /**
     * read as day
     */
    public function asDay(?DatespanRange $range = null): ?Day
    {
        $this->reportError();
        return null;
    }

    /**
     * read as week
     *
     * @since   4.5.0
     */
    public function asWeek(?DatespanRange $range = null): ?Week
    {
        $this->reportError();
        return null;
    }

    /**
     * read as month
     */
    public function asMonth(?DatespanRange $range = null): ?Month
    {
        $this->reportError();
        return null;
    }

    /**
     * read as datespan
     *
     * In case the default value is not of type stubbles\date\span\Datespan an
     * LogicException will be thrown.
     *
     * @since  4.3.0
     */
    public function asDatespan(?DatespanRange $range = null): ?Datespan
    {
        $this->reportError();
        return null;
    }

    /**
     * returns value if it is an ip address, and null otherwise
     */
    public function ifIsIpAddress(): ?string
    {
        $this->reportError();
        return null;
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param  string[]  $allowedValues  list of allowed values
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
     * @param  callable              $predicate  predicate to use
     * @param  string                $errorId    error id to be used in case validation fails
     * @param  array<string,scalar>  $details    optional  details for param error in case validation fails
     * @since  3.0.0
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
     */
    public function withFilter(Filter $filter): mixed
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
     *     ->withCallable(
     *         function(Param $param) {
     *             if ($param->getValue() == 303) {
     *                 return 'Roland TB-303';
     *             }
     *
     *             $param->addErrorWithId('INVALID_303');
     *             return null;
     *          }
     *     );
     * </code>
     */
    public function withCallable(callable $filter): mixed
    {
        $this->reportError();
        return null;
    }
}
