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
use stubbles\lang\SecureString;
/**
 * Interface for password checking algorithms.
 *
 * @since  3.0.0
 * @api
 */
interface PasswordChecker
{
    /**
     * checks given password
     *
     * In case the password does not satisfy the return value is a map of
     * error ids with error details.
     *
     * @param   SecureString  $proposedPassword
     * @return  array
     */
    public function check(SecureString $proposedPassword);
}
