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
use net\stubbles\lang\Object;
/**
 * Interface for validators.
 *
 * Validators allow simple checks whether a value fulfils a set of criteria.
 *
 * @api
 */
interface Validator extends Object
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

    /**
     * returns a list of criteria for the validator
     *
     * @return  array  key is criterion name, value is criterion value
     */
    public function getCriteria();
}
?>