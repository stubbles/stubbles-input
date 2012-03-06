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
/**
 * Class that combines differant validators that all have to be true in order
 * that this validator also reports true.
 *
 * If any of the combined validators returns false the stubAndValidator
 * will return false as well.
 *
 * @api
 */
class AndValidator extends AbstractCompositeValidator
{
    /**
     * validate the given value
     *
     * If any of the validators returns false this will return false as well.
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    protected function doValidate($value)
    {
        foreach ($this->validators as $validator) {
            if ($validator->validate($value) == false) {
                return false;
            }
        }

        return true;
    }
}
?>