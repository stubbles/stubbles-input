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
use stubbles\lang\exception\IllegalStateException;
/**
 * Base class for composite validators.
 *
 * A composite validator can be used to combine two or more validators
 * into a single validator which then applies all those validators for the
 * value to validate.
 */
abstract class AbstractCompositeValidator implements CompositeValidator
{
    /**
     * list of validators to combine
     *
     * @type  Validator[]
     */
    protected $validators = array();

    /**
     * add a validator
     *
     * @param   Validator  $validator
     * @return  CompositeValidator
     */
    public function addValidator(Validator $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * validate the given value
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     * @throws  IllegalStateException
     */
    public function validate($value)
    {
        if (count($this->validators) === 0) {
            throw new IllegalStateException('No validators set for composite ' . get_class($this));
        }

        return $this->doValidate($value);
    }

    /**
     * validate the given value
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    protected abstract function doValidate($value);
}
