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
/**
 * Class that combines differant validators where one has to be true.
 *
 * If any of the combined validators returns true the OrValidator will return
 * true as well.
 *
 * @api
 */
class OrValidator extends AbstractCompositeValidator
{
    /**
     * validate the given value
     *
     * If any of the validators returns true this will return true as well.
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    protected function doValidate($value)
    {
        foreach ($this->validators as $validator) {
            if ($validator->validate($value)) {
                return true;
            }
        }

        return false;
    }
}
