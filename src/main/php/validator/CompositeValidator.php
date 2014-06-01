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
/**
 * Interface for composite validators.
 *
 * Composite validators can be used to combine two or more validators
 * into a single validator.
 *
 * @api
 */
interface CompositeValidator extends Validator
{
    /**
     * add a validator
     *
     * @param   Validator  $validator
     * @return  CompositeValidator
     */
    public function addValidator(Validator $validator);
}
