<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use stubbles\input\Filter;
use stubbles\input\Param;
use stubbles\lang\SecureString;
/**
 * Class for filtering passwords.
 *
 * This filter allows to check password inputs and if they comply with the rules
 * for a password.
 * If the value is an array the fields with key 0 and 1 are compared. If they are
 * not equal the password is not allowed (can be used to prevent mistyped
 * passwords in register or password change forms).
 */
class PasswordFilter implements Filter
{
    /**
     * actual algorithm to check the password with
     *
     * @type  PasswordChecker
     */
    private $checker;

    /**
     * constructor
     *
     * @param  PasswordChecker  $checker
     */
    public function __construct(PasswordChecker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * apply filter on given param
     *
     * @param   Param         $param
     * @return  SecureString  secured password
     */
    public function apply(Param $param)
    {
        $proposedPassword = $this->parse($param);
        if (null === $proposedPassword) {
            return null;
        }

        $errors = $this->checker->check($proposedPassword);
        if (count($errors) > 0) {
            foreach ($errors as $errorId => $details) {
                $param->addError($errorId, $details);
            }

            return null;
        }

        return $proposedPassword;
    }

    /**
     * parses password from given param value
     *
     * @param   Param  $param
     * @return  SecureString
     */
    private function parse(Param $param)
    {
        $value = $param->value();
        if (is_array($value)) {
            if ($value[0] !== $value[1]) {
                $param->addError('PASSWORDS_NOT_EQUAL');
                return null;
            }

            $value = $value[0];
        }

        if (strlen($value) === 0) {
            return null;
        }

        return SecureString::create($value);
    }
}
