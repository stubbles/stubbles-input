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
/**
 * Interface for validators.
 *
 * Validators allow simple checks whether a value fulfils a set of criteria.
 *
 * @api
 * @deprecated  since 3.0.0, use predicates instead, will be removed with 4.0.0
 */
interface Validator
{
    /**
     * validate the given value
     *
     * Returns true if the value does fulfils all of the criteria, else false.
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    public function validate($value);
}
