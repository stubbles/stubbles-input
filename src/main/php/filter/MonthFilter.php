<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\filter;
use stubbles\date\span\Month;
use stubbles\input\Filter;
use stubbles\input\Param;
/**
 * Class for filtering months.
 *
 * The following rules apply:
 * - If given value is empty the returned value is null.
 * - If given value is not a valid month the returned value is null.
 * - If given value is a valid month the returned value is an instance of
 *   stubbles\date\span\Month.
 *
 * @since  2.5.1
 */
class MonthFilter implements Filter
{
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * In case the given value can not be transformed into the target type
     * the return value is null. Additionally the $param instance is filled
     * with a FilterError.
     *
     * @param   \stubbles\input\Param  $param
     * @return  \stubbles\date\span\Month
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return null;
        }

        try {
            return Month::fromString($param->value());
        } catch (\InvalidArgumentException $iae) {
            $param->addError('MONTH_INVALID');
        }

        return null;
    }
}
