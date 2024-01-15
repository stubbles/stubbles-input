<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
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
     */
    public function procure(Request $request, Annotation $annotation): mixed;
}
