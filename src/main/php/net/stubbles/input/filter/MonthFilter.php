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
use net\stubbles\lang\types\datespan\Month;
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for filtering months.
 *
 * The following rules apply:
 * - If given value is empty the returned value is null.
 * - If given value is not a valid month the returned value is null.
 * - If given value is a valid month the returned value is an instance of
 *   net\stubbles\lang\types\datespan\Month.
 *
 * @since  2.5.1
 */
class MonthFilter implements Filter
{
    /**
     * apply filter on given param
     *
     * In case the given value can not be transformed into the target type
     * the return value is null. Additionally the $param instance is filled
     * with a FilterError.
     *
     * @param   Param  $param
     * @return  Date
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return null;
        }

        try {
            return Month::fromString($param->getValue());
        } catch (IllegalArgumentException $iae) {
            $param->addErrorWithId('MONTH_INVALID');
        }

        return null;
    }
}
