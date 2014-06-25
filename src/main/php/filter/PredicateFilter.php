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
use stubbles\predicate\Predicate;
/**
 * Class for filtering values based on validators.
 *
 * @since  3.0.0
 */
class PredicateFilter implements Filter
{
    /**
     * predicate to be used
     *
     * @type  Predicate
     */
    private $predicate;
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
     * @param  \stubbles\predicate\Predicate|callable  $predicate  validator to be used
     * @param  string                                  $errorId    error id to be used in case predicate fails
     * @param  array                                   $details    details for param error in case predicate fails
     */
    public function __construct($predicate, $errorId, array $details = [])
    {
        $this->predicate = Predicate::castFrom($predicate);
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
        if ($this->predicate->test($param->value())) {
            return $param->value();
        }

        $param->addError($this->errorId, $this->details);
        return null;
    }
}
