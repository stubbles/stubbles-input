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
 * Value object for request values to retrieve them after validation.
 *
 * @since  1.3.0
 */
class ValueReader extends BaseObject
{
    /**
     * parameter to filter
     *
     * @type  Param
     */
    private $param;

    /**
     * constructor
     *
     * @param  Param  $param  parameter to read
     */
    public function __construct(Param $param)
    {
        $this->param = $param;
    }

    /**
     * create instance as mock with empty param errors
     *
     * @param   string  $paramValue
     * @return  ValueReader
     */
    public static function mockForValue($paramValue)
    {
        return new self(new Param('mock', $paramValue));
    }

    /**
     * returns value if it contains given string, and null otherwise
     *
     * @api
     * @param   string  $contained  byte sequence the value must contain
     * @param   string  $default    default value to fall back to
     * @return  string
     */
    public function ifContains($contained, $default = null)
    {
        return $this->withValidator(new ContainsValidator($contained), $default);
    }

    /**
     * returns value if it eqals an expected value, and null otherwise
     *
     * @api
     * @param   string  $expected  byte sequence the value must be equal to
     * @param   string  $default   default value to fall back to
     * @return  bool
     */
    public function ifIsEqualTo($expected, $default = null)
    {
        return $this->withValidator(new EqualValidator($expected), $default);
    }

    /**
     * returns value if it is an http url, and null otherwise
     *
     * @api
     * @param   string  $default   default value to fall back to
     * @return  string
     */
    public function ifIsHttpUri($default = null)
    {
        return $this->withValidator(new HttpUriValidator(), $default);
    }

    /**
     * returns value if it is an http url, and null otherwise
     *
     * @api
     * @param   string  $default   default value to fall back to
     * @return  string
     */
    public function ifIsExistingHttpUri($default = null)
    {
        $httpUriValidator = new HttpUriValidator();
        return $this->withValidator($httpUriValidator->enableDnsCheck(), $default);
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @api
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifIsIpAddress($default = null)
    {
        return $this->withValidator(new IpValidator(), $default);
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @api
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifIsMailAddress($default = null)
    {
        return $this->withValidator(new MailValidator(), $default);
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @api
     * @param   string[]  $allowedValues  list of allowed values
     * @param   string    $default        default value to fall back to
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues, $default = null)
    {
        return $this->withValidator(new PreSelectValidator($allowedValues), $default);
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @api
     * @param   string  $regex    regular expression to apply
     * @param   string  $default  default value to fall back to
     * @return  string
     */
    public function ifSatisfiesRegex($regex, $default = null)
    {
        return $this->withValidator(new RegexValidator($regex), $default);
    }

    /**
     * returns value if it denotes a path to an existing file
     *
     * @api
     * @param   string  $basePath       base path where file must reside in
     * @param   string  $default        default value to fall back to
     * @param   bool    $allowRelative  whether relative pathes are allowed
     * @return  string
     * @since   2.0.0
     */
    public function ifIsFile($basePath = null, $default = null, $allowRelative = FilesystemValidator::NO_RELATIVE)
    {
        $fileValidator = new FileValidator($basePath);
        if ($allowRelative) {
            $fileValidator->allowRelative();
        }

        return $this->withValidator($fileValidator, $default);
    }

    /**
     * returns value if it denotes a path to an existing directory
     *
     * @api
     * @param   string  $basePath       base path where directory must reside in
     * @param   string  $default        default value to fall back to
     * @param   bool    $allowRelative  whether relative pathes are allowed
     * @return  string
     * @since   2.0.0
     */
    public function ifIsDirectory($basePath = null, $default = null, $allowRelative = FilesystemValidator::NO_RELATIVE)
    {
        $directoryValidator = new DirectoryValidator($basePath);
        if ($allowRelative) {
            $directoryValidator->allowRelative();
        }

        return $this->withValidator($directoryValidator, $default);
    }

    /**
     * checks value with given validator
     *
     * If value does not satisfy the validator return value will be null.
     *
     * @api
     * @param   Validator  $validator  validator to use
     * @param   string     $default    default value to fall back to
     * @return  string
     */
    public function withValidator(Validator $validator, $default = null)
    {
        if ($this->param->isNull()) {
            return $default;
        }

        if ($validator->validate($this->param->getValue())) {
            return $this->param->getValue();
        }

        return $default;
    }

    /**
     * returns value unvalidated
     *
     * This should be used with greatest care.
     *
     * @api
     * @return  string
     */
    public function unsecure()
    {
        return $this->param->getValue();
    }
}
?>