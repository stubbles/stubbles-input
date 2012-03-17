<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\validator;
use net\stubbles\input\Param;
use net\stubbles\lang\BaseObject;
/**
 * Value object for request values to check them against validators.
 *
 * @since  1.3.0
 */
class ValueValidator extends BaseObject
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
     * @param  Param  $param  original value
     */
    public function __construct(Param $param)
    {
        $this->param = $param;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string  $paramValue
     * @return  ValueValidator
     */
    public static function mockForValue($paramValue)
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
        return $this->withValidator(new ContainsValidator($contained));
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
        return $this->withValidator(new EqualValidator($expected));
    }

    /**
     * checks whether value is an http uri
     *
     * @api
     * @return  bool
     */
    public function isHttpUri()
    {
        return $this->withValidator(new HttpUriValidator());
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
        $validator = new HttpUriValidator();
        return $this->withValidator($validator->enableDnsCheck());
    }

    /**
     * checks whether value is an ip address, where both IPv4 and IPv6 are valid
     *
     * @api
     * @return  bool
     */
    public function isIpAddress()
    {
        return $this->withValidator(new IpValidator());
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
        return $this->withValidator(new IpV4Validator());
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
        return $this->withValidator(new IpV6Validator());
    }

    /**
     * checks whether value is a mail address
     *
     * @api
     * @return  string
     */
    public function isMailAddress()
    {
        return $this->withValidator(new MailValidator());
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
        return $this->withValidator(new PreSelectValidator($allowedValues));
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
        return $this->withValidator(new RegexValidator($regex));
    }

    /**
     * checks value with given validator
     *
     * @api
     * @param   Validator  $validator  validator to use
     * @return  bool
     */
    public function withValidator(Validator $validator)
    {
        return $validator->validate($this->param->getValue());
    }
}
?>