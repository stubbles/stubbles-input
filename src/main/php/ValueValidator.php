<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input;
use stubbles\input\Param;
use stubbles\predicate\Contains;
use stubbles\predicate\Equals;
use stubbles\predicate\IsExistingHttpUri;
use stubbles\predicate\IsHttpUri;
use stubbles\predicate\IsIpAddress;
use stubbles\predicate\IsIpV4Address;
use stubbles\predicate\IsIpV6Address;
use stubbles\predicate\IsMailAddress;
use stubbles\predicate\IsOneOf;
use stubbles\predicate\Predicate;
use stubbles\predicate\Regex;
/**
 * Value object for request values to check them against validators.
 *
 * @since  1.3.0
 */
class ValueValidator
{
    /**
     * original value
     *
     * @type  string
     */
    private $param;

    /**
     * constructor
     *
     * @param  \stubbles\input\Param  $param  original value
     */
    public function __construct(Param $param)
    {
        $this->param = $param;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string  $paramValue
     * @return  \stubbles\input\ValueValidator
     */
    public static function forValue($paramValue)
    {
        return new self(new Param('mock', $paramValue));
    }

    /**
     * checks whether value contains given string
     *
     * @api
     * @param   string  $contained  byte sequence the value must contain
     * @return  bool
     */
    public function contains($contained)
    {
        return $this->with(new Contains($contained));
    }


    /**
     * checks whether value equals given string
     *
     * @api
     * @param   string  $expected   byte sequence the value must be equal to
     * @return  bool
     */
    public function isEqualTo($expected)
    {
        return $this->with(new Equals($expected));
    }

    /**
     * checks whether value is an http uri
     *
     * @api
     * @return  bool
     */
    public function isHttpUri()
    {
        return $this->with(IsHttpUri::instance());
    }

    /**
     * checks whether value is an existing http uri
     *
     * @api
     * @return  bool
     * @since   2.0.0
     */
    public function isExistingHttpUri()
    {
        return $this->with(IsExistingHttpUri::instance());
    }

    /**
     * checks whether value is an ip address, where both IPv4 and IPv6 are valid
     *
     * @api
     * @return  bool
     */
    public function isIpAddress()
    {
        return $this->with(IsIpAddress::instance());
    }

    /**
     * checks whether value is an ip v4 address
     *
     * @api
     * @return  bool
     * @since   1.7.0
     */
    public function isIpV4Address()
    {
        return $this->with(IsIpV4Address::instance());
    }

    /**
     * checks whether value is an ip v6 address
     *
     * @api
     * @return  bool
     * @since   1.7.0
     */
    public function isIpV6Address()
    {
        return $this->with(IsIpV6Address::instance());
    }

    /**
     * checks whether value is a mail address
     *
     * @api
     * @return  string
     */
    public function isMailAddress()
    {
        return $this->with(IsMailAddress::instance());
    }

    /**
     * checks whether value is in a list of allowed values
     *
     * @api
     * @param   string[]  $allowedValues  list of allowed values
     * @return  bool
     */
    public function isOneOf(array $allowedValues)
    {
        return $this->with(new IsOneOf($allowedValues));
    }

    /**
     * checks whether value satisfies given regular expression
     *
     * @api
     * @param   string  $regex  regular expression to apply
     * @return  bool
     */
    public function satisfiesRegex($regex)
    {
        return $this->with(new Regex($regex));
    }

    /**
     * evaluates value with given predicate
     *
     * Given predicate can either be an instance of \stubbles\predicate\Predicate
     * or any callable which returns a boolean value.
     *
     * @api
     * @param   \stubbles\predicate\Predicate|callable  $predicate  predicate to use
     * @return  bool
     * @since   3.0.0
     */
    public function with($predicate)
    {
        return Predicate::castFrom($predicate)->test($this->param->value());
    }
}
