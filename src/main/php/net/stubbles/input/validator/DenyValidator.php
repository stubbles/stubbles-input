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
use net\stubbles\input\Validator;
/**
 * Validator that denies validaty of values.
 *
 * @api
 */
class DenyValidator implements Validator
{
    /**
     * validate that the given value complies with the regular expression
     *
     * @param   mixed  $value
     * @return  bool   always false
     */
    public function validate($value)
    {
        return false;
    }

}
?>