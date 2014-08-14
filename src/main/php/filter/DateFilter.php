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
use stubbles\date\Date;
use stubbles\input\Filter;
use stubbles\input\Param;
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for filtering dates.
 *
 * The following rules apply:
 * - If given value is empty the returned value is null.
 * - If given value is not a valid date the returned value is null.
 * - If given value is a valid date the returned value is an instance of
 *   stubbles\date\Date.
 *
 * @see  http://php.net/manual/de/datetime.formats.php
 */
class DateFilter implements Filter
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
     * @return  \stubbles\date\Date
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return null;
        }

        try {
            return new Date($param->value());
        } catch (IllegalArgumentException $iae) {
            $param->addError('DATE_INVALID');
        } catch (\InvalidArgumentException $iae) {
            $param->addError('DATE_INVALID');
        }

        return null;
    }
}
