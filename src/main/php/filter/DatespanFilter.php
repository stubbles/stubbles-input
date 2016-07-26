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
use stubbles\date\span;
use stubbles\input\Filter;
use stubbles\input\Param;
/**
 * Class for filtering datespans.
 *
 * @since  4.3.0
 */
class DatespanFilter implements Filter
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
     * @return  \stubbles\date\span\Datespan
     */
    public function apply(Param $param)
    {
        if ($param->isEmpty()) {
            return null;
        }

        try {
            return span\parse($param->value());
        } catch (\InvalidArgumentException $iae) {
            $param->addError('DATESPAN_INVALID');
        }

        return null;
    }
}
