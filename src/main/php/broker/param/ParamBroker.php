<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\reflect\annotation\Annotation;
/**
 * Broker to be used to filter parameters based on annotations.
 *
 * @api
 */
interface ParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\Request                  $request     instance to handle value with
     * @param   \stubbles\reflect\annotation\Annotation  $annotation  annotation which contains request param metadata
     * @return  mixed
     */
    public function procure(Request $request, Annotation $annotation);

    /**
     * handles a single param
     *
     * @param   \stubbles\input\Param                    $param
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @return  mixed
     * @deprecated  since 7.0.0, will be removed with 8.0.0
     */
    public function procureParam(Param $param, Annotation $annotation);
}
