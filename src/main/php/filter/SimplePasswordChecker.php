<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\filter;
use stubbles\values\Secret;
/**
 * Interface for password checking algorithms.
 *
 * This checker checks a proposed password against three conditions:
 * - it must have a minimum length (default: 8)
 * - it must have a minimum of different characters (default: 5)
 * - it must not be one of disallowed values (default: none)
 *
 * @since  3.0.0
 */
class SimplePasswordChecker implements PasswordChecker
{
    /**
     * default amount of different characters in password
     */
    const DEFAULT_MIN_DIFF_CHARS = 5;
    /**
     * defailt minimum length of password
     */
    const DEFAULT_MINLENGTH      = 8;
    /**
     * minimum amount of different characters in the password
     *
     * @var  int
     */
    private $minDiffChars        = self::DEFAULT_MIN_DIFF_CHARS;
    /**
     * list of values that are not allowed as password
     *
     * @var  string[]
     */
    private $disallowedValues    = [];
    /**
     * minimum length of password
     *
     * @var  int
     */
    private $minLength           = self::DEFAULT_MINLENGTH;

    /**
     * static constructor
     *
     * @return  \stubbles\input\filter\SimplePasswordChecker
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * set a list of values that are not allowed as password
     *
     * @param   string[]  $values  list of values that are not allowed as password
     * @return  \stubbles\input\filter\SimplePasswordChecker
     */
    public function disallowValues(array $values): self
    {
        $this->disallowedValues = $values;
        return $this;
    }

    /**
     * set minimum amount of different characters within password
     *
     * Set the value with NULL to disable the check.
     *
     * @param   int  $minDiffChars
     * @return  \stubbles\input\filter\SimplePasswordChecker
     */
    public function minDiffChars(int $minDiffChars): self
    {
        $this->minDiffChars = $minDiffChars;
        return $this;
    }

    /**
     * sets minimum length of password
     *
     * @param   int  $minLength
     * @return  \stubbles\input\filter\SimplePasswordChecker
     */
    public function minLength(int $minLength): self
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * checks given password
     *
     * In case the password does not satisfy the return value is a list of
     * error ids.
     *
     * @param   \stubbles\values\Secret  $proposedPassword
     * @return  array<string,array<string,scalar>>
     */
    public function check(Secret $proposedPassword): array
    {
        $errors = [];
        if ($proposedPassword->length() < $this->minLength) {
            $errors['PASSWORD_TOO_SHORT'] = ['minLength' => $this->minLength];
        }

        if (in_array($proposedPassword->unveil(), $this->disallowedValues)) {
            $errors['PASSWORD_DISALLOWED'] = [];
        }


        if (null !== $this->minDiffChars
          // cast to string after unveiling because count_chars()
          // expects a string, so null becomes an empty string
          && count(count_chars((string) $proposedPassword->unveil(), 1)) < $this->minDiffChars) {
            $errors['PASSWORD_TOO_LESS_DIFF_CHARS'] = ['minDiff' => $this->minDiffChars];
        }

        return $errors;
    }
}
