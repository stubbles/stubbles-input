<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter;
use net\stubbles\input\Filter;
use net\stubbles\input\Param;
use net\stubbles\input\ParamError;
use net\stubbles\input\Validator;
use net\stubbles\lang\BaseObject;
/**
 * Class for filtering values based on validators.
 *
 * @since  2.0.0
 */
class ValidatingFilter extends BaseObject implements Filter
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
    public function __construct(Validator $validator, $errorId, array $details = array())
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
        if ($this->validator->validate($param->getValue())) {
            return $param->getValue();
        }

        $param->addError(new ParamError($this->errorId, $this->details));
        return null;
    }
}
?>