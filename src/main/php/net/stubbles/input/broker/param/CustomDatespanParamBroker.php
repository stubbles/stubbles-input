<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker\param;
use net\stubbles\input\Request;
use net\stubbles\input\filter\expectation\DateExpectation;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\types\datespan\CustomDatespan;
/**
 * Filter parameters based on a @Request[CustomDatespan] annotation.
 */
class CustomDatespanParamBroker extends BaseObject implements ParamBroker
{
    /**
     * handles single param
     *
     * @param   Request     $request     instance to handle value with
     * @param   Annotation  $annotation  annotation which contains request param metadata
     * @return  net\stubbles\lang\types\datespan\CustomDatespan
     */
    public function procure(Request $request, Annotation $annotation)
    {
        $expect = DateExpectation::fromAnnotation($annotation);
        try {
            return new CustomDatespan($this->getDate($request, $annotation->getStartName(), $expect),
                                    $this->getDate($request, $annotation->getEndName(), $expect)
                );
        } catch (\Exception $e) {
            return null;
        }

    }

    /**
     * handles single param
     *
     * @param   Request          $request
     * @param   string           $name
     * @param   DateExpectation  $expect
     * @return  net\stubbles\lang\types\Date
     */
    private function getDate(Request $request, $name, $expect)
    {
        return $request->filterParam($name)->asDate($expect);
    }
}
?>