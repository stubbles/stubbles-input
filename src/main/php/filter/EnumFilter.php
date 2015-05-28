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
/**
 * Tries to deduct an enum instance from a given param value.
 *
 * @since  5.0.0
 */
class EnumFilter implements Filter
{
    /**
     * @type  string
     */
    private $enumClass;

    /**
     * constructor
     *
     * @param   string  $enumClass  name of enum class to derive filtered value from
     * @throws  \InvalidArgumentException
     */
    public function __construct($enumClass)
    {
        if (!class_exists($enumClass)) {
            throw new \InvalidArgumentException(
                    'Given class ' . $enumClass . ' does not exist'
            );
        }

        if (!is_subclass_of($enumClass, 'stubbles\lang\Enum')) {
            throw new \InvalidArgumentException(
                    'Given class ' . $enumClass
                    . ' doesn\'t seem to be an instance of stubbles\lang\Enum'
            );
        }

        $this->enumClass = $enumClass;
    }

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  \stubbles\lang\Enum
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return null;
        }

        $enum = $this->enumClass;
        try {
            return $enum::forName(strtoupper($param->value()));
        } catch (\InvalidArgumentException $iae) {
            $param->addError(
                    'FIELD_NO_SELECT',
                    ['ALLOWED' => join('|', $enum::namesOf())]
            );
            return null;
        }
    }
}
