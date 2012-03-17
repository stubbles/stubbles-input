<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\filter\expectation;
use net\stubbles\input\Param;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalStateException;
/**
 * Description of value expectation.
 *
 * @since  2.0.0
 */
class ValueExpectation extends BaseObject
{/**
     * switch whether a value is expected
     *
     * @type  bool
     */
    private $required;
    /**
     * default value to be used if no value given
     *
     * @type  mixed
     */
    protected $default;

    /**
     * constructor
     *
     * @param  bool   $required
     */
    protected function __construct($required)
    {
        $this->required = $required;
    }

    /**
     * creates an expectation where a value is required
     *
     * @return  ValueExpectation
     */
    public static function createAsRequired()
    {
        return new self(true);
    }

    /**
     * creates an expectation where no value is required
     *
     * @return  ValueExpectation
     */
    public static function create()
    {
        return new self(false);
    }

    /**
     * use default value if no value available
     *
     * @param   mixed  $default
     * @return  ValueExpectation
     */
    public function useDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * checks whether parameter satisfies expectation
     *
     * @param   Param  $param
     * @return  bool
     */
    public function isSatisfied(Param $param)
    {
        if ($this->required && $param->isEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * checks whether default value should be used
     *
     * @param   bool  $required
     * @return  bool
     */
    public function allowsDefault(Param $param)
    {
        return ($param->isNull() && false === $this->required);
    }

    /**
     * returns default value
     *
     * @return  mixed
     */
    public function getDefault()
    {
        return $this->default;
    }
}
?>