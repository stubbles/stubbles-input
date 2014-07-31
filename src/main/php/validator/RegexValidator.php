<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\validator;
use stubbles\input\Validator;
use stubbles\lang\exception\RuntimeException;
/**
 * Validator to ensure a value complies to a given regular expression.
 *
 * The validator uses preg_match() and checks if the value occurs exactly
 * one time. Please make sure that the supplied regular expresion contains
 * correct delimiters, they will not be applied automatically. The validate()
 * method throws a runtime exception in case the regular expression is invalid.
 *
 * @api
 * @deprecated  since 3.0.0, use stubbles\predicate\Regex instead, will be removed with 4.0.0
 */
class RegexValidator implements Validator
{
    /**
     * the regular expression to use for validation
     *
     * @type  string
     */
    private $regex;

    /**
     * constructor
     *
     * @param  string  $regex  regular expression to use for validation
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    /**
     * validate that the given value complies with the regular expression
     *
     * @param   mixed  $value
     * @return  bool   true if value complies with regular expression, else false
     * @throws  \stubbles\lang\exception\RuntimeException  in case the used regular expresion is invalid
     */
    public function validate($value)
    {
        $check = @preg_match($this->regex, $value);
        if (false === $check) {
            throw new RuntimeException('Invalid regular expression ' . $this->regex);
        }

        return ((1 != $check) ? (false) : (true));
    }
}
