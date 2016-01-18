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
use stubbles\input\predicate\Predicate;
use stubbles\peer\IpAddress;
use stubbles\peer\http\HttpUri;

use function stubbles\input\predicate\contains;
use function stubbles\input\predicate\containsAnyOf;
use function stubbles\input\predicate\equals;
use function stubbles\input\predicate\isOneOf;
use function stubbles\peer\isMailAddress;
use function stubbles\values\pattern;
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
     * @param   string  $needle  byte sequence the value must contain
     * @return  bool
     */
    public function contains($needle)
    {
        return contains($needle)->test($this->param->value());
    }

    /**
     * checks whether value contains any of the given strings
     *
     * @api
     * @param   string[]  $contained
     * @return  bool
     * @since   4.3.0
     */
    public function containsAnyOf(array $contained)
    {
        return containsAnyOf($contained)->test($this->param->value());
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
        return equals($expected)->test($this->param->value());
    }

    /**
     * checks whether value is an http uri
     *
     * @api
     * @return  bool
     */
    public function isHttpUri()
    {
        return HttpUri::isValid($this->param->value());
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
        return HttpUri::exists($this->param->value());
    }

    /**
     * checks whether value is an ip address, where both IPv4 and IPv6 are valid
     *
     * @api
     * @return  bool
     */
    public function isIpAddress()
    {
        return IpAddress::isValid($this->param->value());
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
        return IpAddress::isValidV4($this->param->value());
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
        return IpAddress::isValidV6($this->param->value());
    }

    /**
     * checks whether value is a mail address
     *
     * @api
     * @return  string
     */
    public function isMailAddress()
    {
        return isMailAddress($this->param->value());
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
        return isOneOf($allowedValues)->test($this->param->value());
    }

    /**
     * checks whether value satisfies given regular expression
     *
     * @api
     * @param   string  $regex  regular expression to apply
     * @return  bool
     * @since   6.0.0
     */
    public function matches($regex)
    {
        return pattern($regex)->matches($this->param->value());
    }

    /**
     * checks whether value satisfies given regular expression
     *
     * @api
     * @param   string  $regex  regular expression to apply
     * @return  bool
     * @deprecated  since 6.0.0, use matches() instead, will be removed with 7.0.0
     */
    public function satisfiesRegex($regex)
    {
        return $this->matches($regex);
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
