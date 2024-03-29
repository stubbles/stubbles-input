<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;

use SensitiveParameter;
use stubbles\input\Filter;
use stubbles\values\Secret;
use stubbles\values\Value;
/**
 * Class for filtering passwords.
 *
 * This filter allows to check password inputs and if they comply with the rules
 * for a password.
 * If the value is an array the fields with key 0 and 1 are compared. If they are
 * not equal the password is not allowed (can be used to prevent mistyped
 * passwords in register or password change forms).
 */
class PasswordFilter extends Filter
{

    public function __construct(private PasswordChecker $checker) { }

    /**
     * apply filter on given value
     *
     * @return  mixed[]
     */
    public function apply(Value $value): array
    {
        if ($value->isEmpty()) {
            return $this->null();
        }

        list($proposedPassword, $errors) = $this->parse($value->value());
        if (count($errors) > 0) {
            return $this->errors($errors);
        }

        if (null === $proposedPassword) {
            return $this->null();
        }

        $errors = $this->checker->check($proposedPassword);
        if (count($errors) > 0) {
            return $this->errors($errors);
        }

        return $this->filtered($proposedPassword);
    }

    /**
     * parses password from given param value
     *
     * @param   string|string[]  $value
     * @return  mixed[]
     */
    private function parse(#[SensitiveParameter] mixed $value): array
    {
        if (is_array($value)) {
            if ($value[0] !== $value[1]) {
                return $this->error('PASSWORDS_NOT_EQUAL');
            }

            $value = $value[0];
        }

        if (empty($value)) {
            return [null, []];
        }

        return [Secret::create($value), []];
    }
}
