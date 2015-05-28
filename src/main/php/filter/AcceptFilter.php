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
use stubbles\peer\http\AcceptHeader;
/**
 * Filters accept headers.
 *
 * @since  2.0.1
 */
class AcceptFilter implements Filter
{
    use ReusableFilter;

    /**
     * apply filter on given param
     *
     * @param   \stubbles\input\Param  $param
     * @return  \stubbles\peer\http\AcceptHeader
     */
    public function apply(Param $param)
    {
        if ($param->isNull() || $param->isEmpty()) {
            return new AcceptHeader();
        }

        try {
            return AcceptHeader::parse($param->value());
        } catch (\InvalidArgumentException $iae) {
            return new AcceptHeader();
        }
    }
}
