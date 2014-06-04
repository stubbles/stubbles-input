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
use stubbles\input\Filter;
use stubbles\input\Param;
use stubbles\input\ParamError;
use stubbles\input\Validator;
/**
 * Class for filtering values based on validators.
 *
 * @since  2.0.0
 */
class ValidatingFilter implements Filter
{
    /**
     * validator to be used
     *
     * @type  Validator
     */
    private $validator;
    /**
     * error id to be used in case validation fails
     *
     * @type  string
     */
    private $errorId;
    /**
     * details for param error in case validation fails
     *
     * @type  array
     */
    private $details;

    /**
     * constructor
     *
     * @param  Validator  $validator  validator to be used
     * @param  string     $errorId    error id to be used in case validation fails
     * @param  array      $details    details for param error in case validation fails
     */
    public function __construct(Validator $validator, $errorId, array $details = [])
    {
        $this->validator = $validator;
        $this->errorId   = $errorId;
        $this->details   = $details;
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  string
     */
    public function apply(Param $param)
    {
        if ($this->validator->validate($param->value())) {
            return $param->value();
        }

        $param->addError($this->errorId, $this->details);
        return null;
    }
}
