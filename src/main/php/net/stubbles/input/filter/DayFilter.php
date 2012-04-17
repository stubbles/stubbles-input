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
use net\stubbles\input\Param;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\types\datespan\Day;
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for filtering dates.
 *
 * The following rules apply:
 * - If given value is empty the returned value is null.
 * - If given value is not a valid date the returned value is null.
 * - If given value is a valid date the returned value is an instance of
 *   net\stubbles\lang\types\datespan\Day.
 *
 * @see  http://php.net/manual/de/datetime.formats.php
 */
class DayFilter extends BaseObject implements Filter
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
            return new Day($param->getValue());
        } catch (IllegalArgumentException $iae) {
            $param->addErrorWithId('DATE_INVALID');
        }

        return null;
    }
}
?>