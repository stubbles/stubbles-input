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
 * Basic class for filters on request variables of type boolean.
 *
 * If given value is 1 (int or string), 'true' (string) or true (boolean) the
 * filter returns boolean true; and boolean false in all other cases.
 *
 * @since  1.2.0
 */
class BoolFilter implements Filter
{
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  bool
     */
    public function apply(Param $param)
    {
        if (in_array($param->value(), [1, '1', 'true', true, 'yes'], true)) {
            return true;
        }

        return false;
    }
}
