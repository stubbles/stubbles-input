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
    /**
     * apply filter on given param
     *
     * @param   Param  $param
     * @return  bool
     */
    public function apply(Param $param)
    {
        if (in_array($param->getValue(), array(1, '1', 'true', true, 'yes'), true)) {
            return true;
        }

        return false;
    }
}
