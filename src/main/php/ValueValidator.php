<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input;
use stubbles\values\Value;
/**
 * Value object for request values to check them against validators.
 *
 * @since  1.3.0
 */
class ValueValidator
{
    public function __construct(private Value $value) { }

    /**
     * create instance as mock with empty param errors
     */
    public static function forValue(string $paramValue): self
    {
        return new self(Value::of($paramValue));
    }

    /**
     * checks whether value contains given string
     *
     * @api
     */
    public function contains(string $needle): bool
    {
        return $this->value->contains($needle);
    }

    /**
     * checks whether value contains any of the given elements
     *
     * @api
     * @param   string[]  $elements
     * @since   4.3.0
     */
    public function containsAnyOf(array $elements): bool
    {
        return $this->value->containsAnyOf($elements);
    }


    /**
     * checks whether value equals given string
     *
     * @api
     */
    public function isEqualTo(string $expected): bool
    {
        return $this->value->equals($expected);
    }

    /**
     * checks whether value is an http uri
     *
     * @api
     */
    public function isHttpUri(): bool
    {
        return $this->value->isHttpUri();
    }

    /**
     * checks whether value is an existing http uri
     *
     * @api
     * @since   2.0.0
     */
    public function isExistingHttpUri(): bool
    {
        return $this->value->isExistingHttpUri();
    }

    /**
     * checks whether value is an ip address, where both IPv4 and IPv6 are valid
     *
     * @api
     */
    public function isIpAddress(): bool
    {
        return $this->value->isIpAddress();
    }

    /**
     * checks whether value is an ip v4 address
     *
     * @api
     * @since  1.7.0
     */
    public function isIpV4Address(): bool
    {
        return $this->value->isIpV4Address();
    }

    /**
     * checks whether value is an ip v6 address
     *
     * @api
     * @since  1.7.0
     */
    public function isIpV6Address(): bool
    {
        return $this->value->isIpV6Address();
    }

    /**
     * checks whether value is a mail address
     *
     * @api
     */
    public function isMailAddress(): bool
    {
        return $this->value->isMailAddress();
    }

    /**
     * checks whether value is in a list of allowed values
     *
     * @api
     * @param  string[]  $allowedValues  list of allowed values
     */
    public function isOneOf(array $allowedValues): bool
    {
        return $this->value->isOneOf($allowedValues);
    }

    /**
     * checks whether value satisfies given regular expression
     *
     * @api
     * @since  6.0.0
     */
    public function matches(string $regex): bool
    {
        return $this->value->isMatchedBy($regex);
    }

    /**
     * evaluates value with given predicate
     *
     * Given predicate can be any callable which accepts an instance of
     * stubbles\values\Value and returns a boolean value.
     *
     * @api
     * @since  3.0.0
     */
    public function with(callable $predicate): bool
    {
        return $predicate($this->value);
    }
}
