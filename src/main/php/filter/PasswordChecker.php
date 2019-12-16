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
     * @param   \stubbles\values\Secret  $proposedPassword
     * @return  array<string,array<string,mixed>>
     */
    public function check(Secret $proposedPassword): array;
}
