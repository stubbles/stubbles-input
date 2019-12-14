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
    /**
     * @var  \stubbles\values\Value
     */
    private $value;

    /**
     * constructor
     *
     * @param  \stubbles\values\Value  $value  original value
     */
    public function __construct(Value $value)
    {
        $this->value = $value;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string  $paramValue
     * @return  \stubbles\input\ValueValidator
     */
    public static function forValue($paramValue): self
    {
        return new self(Value::of($paramValue));
    }

    /**
     * checks whether value contains given string
     *
     * @api
     * @param   string  $needle  byte sequence the value must contain
     * @return  bool
     */
    public function contains($needle): bool
    {
        return $this->value->contains($needle);
    }

    /**
     * checks whether value contains any of the given elements
     *
     * @api
     * @param   string[]  $elements
     * @return  bool
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
     * @param   string  $expected   byte sequence the value must be equal to
     * @return  bool
     */
    public function isEqualTo($expected): bool
    {
        return $this->value->equals($expected);
    }

    /**
     * checks whether value is an http uri
     *
     * @api
     * @return  bool
     */
    public function isHttpUri(): bool
    {
        return $this->value->isHttpUri();
    }

    /**
     * checks whether value is an existing http uri
     *
     * @api
     * @return  bool
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
     * @return  bool
     */
    public function isIpAddress(): bool
    {
        return $this->value->isIpAddress();
    }

    /**
     * checks whether value is an ip v4 address
     *
     * @api
     * @return  bool
     * @since   1.7.0
     */
    public function isIpV4Address(): bool
    {
        return $this->value->isIpV4Address();
    }

    /**
     * checks whether value is an ip v6 address
     *
     * @api
     * @return  bool
     * @since   1.7.0
     */
    public function isIpV6Address(): bool
    {
        return $this->value->isIpV6Address();
    }

    /**
     * checks whether value is a mail address
     *
     * @api
     * @return  bool
     */
    public function isMailAddress(): bool
    {
        return $this->value->isMailAddress();
    }

    /**
     * checks whether value is in a list of allowed values
     *
     * @api
     * @param   string[]  $allowedValues  list of allowed values
     * @return  bool
     */
    public function isOneOf(array $allowedValues): bool
    {
        return $this->value->isOneOf($allowedValues);
    }

    /**
     * checks whether value satisfies given regular expression
     *
     * @api
     * @param   string  $regex  regular expression to apply
     * @return  bool
     * @since   6.0.0
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
     * @param   callable  $predicate  predicate to use
     * @return  bool
     * @since   3.0.0
     */
    public function with(callable $predicate): bool
    {
        return $predicate($this->value);
    }
}
