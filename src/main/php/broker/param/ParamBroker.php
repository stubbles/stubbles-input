<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker to be used to filter parameters based on annotations.
 */
interface ParamBroker
{
    /**
     * handles single param
     *
     * @param   Request     $request     instance to handle value with
     * @param   Annotation  $annotation  annotation which contains request param metadata
     * @return  mixed
     */
    public function procure(Request $request, Annotation $annotation);

    /**
     * handles a single param
     *
     * @param   Param       $param
     * @param   Annotation  $annotation
     * @return  mixed
     */
    public function procureParam(Param $param, Annotation $annotation);
}
