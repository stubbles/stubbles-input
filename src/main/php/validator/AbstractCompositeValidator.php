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
use stubbles\input\Validator;
use stubbles\lang\exception\IllegalStateException;
/**
 * Base class for composite validators.
 *
 * A composite validator can be used to combine two or more validators
 * into a single validator which then applies all those validators for the
 * value to validate.
 *
 * @deprecated  since 3.0.0, use predicates instead, will be removed with 4.0.0
 */
abstract class AbstractCompositeValidator implements CompositeValidator
{
    /**
     * list of validators to combine
     *
     * @type  \stubbles\input\Validator[]
     */
    protected $validators = [];

    /**
     * add a validator
     *
     * @param   \stubbles\input\Validator  $validator
     * @return  \stubbles\input\validator\CompositeValidator
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
     * @throws  \stubbles\lang\exception\IllegalStateException
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
