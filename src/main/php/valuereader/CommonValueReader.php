<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\valuereader;

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
 * Interface for common value readings.
 *
 * @since  3.0.0
 */
interface CommonValueReader
{
    /**
     * default separator to be used to split string
     */
    public const string ARRAY_SEPARATOR = ',';

    /**
     * read as array value
     *
     * @param   string  $separator  optional  character to split input value with
     * @return  mixed[]
     */
    public function asArray(string $separator = self::ARRAY_SEPARATOR): ?array;

    /**
     * read as boolean value
     */
    public function asBool(): ?bool;

    /**
     * read as integer value
     */
    public function asInt(?NumberRange $range = null): ?int;

    /**
     * read as float value
     */
    public function asFloat(?NumberRange $range = null, ?int $decimals = null): ?float;

    /**
     * read as string value
     */
    public function asString(?StringLength $length = null): ?string;

    /**
     * read as secret
     */
    public function asSecret(?SecretMinLength $length = null): ?Secret;

    /**
     * read as text value
     *
     * @param   string[]  $allowedTags  list of allowed tags
     */
    public function asText(?StringLength $length = null, array $allowedTags = []): ?string;

    /**
     * read as json value
     *
     * @param   int  $maxLength  maximum allowed length of incoming JSON document in bytes
     * @return  stdClass|array<mixed>|null
     */
    public function asJson(int $maxLength = JsonFilter::DEFAULT_MAX_LENGTH): stdClass|array|null;

    /**
     * read as password value
     */
    public function asPassword(PasswordChecker $checker): ?Secret;

    /**
     * read as http uri
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * LogicException will be thrown.
     */
    public function asHttpUri(): ?HttpUri;

    /**
     * read as http uri if it does exist
     *
     * In case the default value is not of type stubbles\peer\http\HttpUri an
     * LogicException will be thrown.
     */
    public function asExistingHttpUri(): ?HttpUri;

    /**
     * returns value if it is a mail address, and null otherwise
     */
    public function asMailAddress(): ?string;

    /**
     * read as date value
     *
     * In case the default value is not of type stubbles\date\Date an LogicException
     * will be thrown.
     */
    public function asDate(?DateRange $range = null): ?Date;

    /**
     * read as day
     *
     * In case the default value is not of type stubbles\date\span\Day an
     * LogicException will be thrown.
     */
    public function asDay(?DatespanRange $range = null): ?Day;

    /**
     * read as week
     *
     * In case the default value is not of type stubbles\date\span\Week an
     * LogicException will be thrown.
     * @since   4.5.0
     */
    public function asWeek(?DatespanRange $range = null): ?Week;

    /**
     * read as month
     *
     * In case the default value is not of type stubbles\date\span\Month an
     * LogicException will be thrown.
     */
    public function asMonth(?DatespanRange $range = null): ?Month;

    /**
     * read as datespan
     *
     * In case the default value is not of type stubbles\date\span\Datespan an
     * LogicException will be thrown.
     * @since   4.3.0
     */
    public function asDatespan(?DatespanRange $range = null): ?Datespan;

    /**
     * returns value if it is an ip address, and null otherwise
     */
    public function ifIsIpAddress(): ?string;

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param  string[]  $allowedValues  list of allowed values
     */
    public function ifIsOneOf(array $allowedValues): ?string;

    /**
     * returns value if it is matched by given regular expression
     *
     * @since   6.0.0
     */
    public function ifMatches(string $regex): ?string;

    /**
     * returns param value when given predicate evaluates to true
     *
     * If value does not satisfy the predicate return value will be null.
     *
     * @api
     * @param   callable              $predicate  predicate to use
     * @param   string                $errorId    error id to be used in case validation fails
     * @param   array<string,scalar>  $details    optional  details for param error in case validation fails
     * @since   3.0.0
     */
    public function when(callable $predicate, string $errorId, array $details = []): ?string;

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * If it is required but value is null an error will be added to the list
     * of param errors.
     */
    public function withFilter(Filter $filter): mixed;

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
    public function withCallable(callable $filter): mixed;
}
