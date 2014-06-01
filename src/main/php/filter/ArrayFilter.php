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
 * Basic class for filters on request variables of type array.
 *
 * When input param is null return value is null, if input param is an empty
 * string return value is an empty array. For all other values the given param
 * will be split using the separator (defaults to ',') and each array element
 * will be trimmed to remove superfluous whitespace.
 *
 * @since  2.0.0
 */
class ArrayFilter implements Filter
{
    /**
     * default separator to be used to split string
     */
    const SEPARATOR_DEFAULT = ',';
    /**
     * separator to be used to split parameter value
     *
     * @type  string
     */
    private $separator      = self::SEPARATOR_DEFAULT;

    /**
     * sets separator to be used
     *
     * @param   string  $separator
     * @return  ArrayFilter
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  array
     */
    public function apply(Param $param)
    {
        if ($param->isNull()) {
            return null;
        }

        if ($param->isEmpty()) {
            return [];
        }

        return array_map('trim', explode($this->separator, $param->getValue()));
    }
}
