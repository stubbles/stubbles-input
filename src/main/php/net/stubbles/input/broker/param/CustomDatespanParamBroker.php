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
 * Filter parameters based on a @Filter[CustomDatespanFilter] annotation.
 */
class CustomDatespanParamBroker extends BaseObject implements ParamBroker
{
    /**
     * handles single param
     *
     * @param   Request      $request     instance to handle value with
     * @param   Annotation   $annotation  annotation which contains filter metadata
     * @return  net\stubbles\lang\types\datespan\CustomDatespan
     */
    public function handle(Request $request, Annotation $annotation)
    {
        $expect = DateExpectation::fromAnnotation($annotation);
        try {
            return new CustomDatespan($this->getDate($request, $annotation->getStartFieldName(), $expect),
                                    $this->getDate($request, $annotation->getEndFieldName(), $expect)
                );
        } catch (\Exception $e) {
            return null;
        }

    }

    /**
     * handles single param
     *
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  net\stubbles\lang\types\Date
     */
    private function getDate(Request $request, $fieldName, $expect)
    {
        return $request->filterParam($fieldName)->asDate($expect);
    }
}
?>