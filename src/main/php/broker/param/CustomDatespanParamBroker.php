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
use stubbles\date\Date;
use stubbles\date\span\CustomDatespan;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\input\filter\range\DateRange;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Filter parameters based on a @Request[CustomDatespan] annotation.
 */
class CustomDatespanParamBroker implements ParamBroker
{
    /**
     * handles single param
     *
     * @param   Request     $request     instance to handle value with
     * @param   Annotation  $annotation  annotation which contains request param metadata
     * @return  CustomDatespan
     */
    public function procure(Request $request, Annotation $annotation)
    {
        $startDate = $this->getStartDate($request, $annotation);
        $endDate   = $this->getEndDate($request, $annotation);
        if (null !== $startDate && null !== $endDate) {
            return new CustomDatespan($startDate, $endDate);
        }

        return null;
    }

    /**
     * handles a single param
     *
     * @param   Param       $param
     * @param   Annotation  $annotation
     * @return  mixed
     */
    public function procureParam(Param $param, Annotation $annotation)
    {
        throw new \stubbles\lang\exception\MethodNotSupportedException('Can not procure a single param');
    }

    /**
     * retrieves start date
     *
     * @param   Request    $request
     * @param   string     $name
     * @return  Date
     */
    private function getStartDate(Request $request, Annotation $annotation)
    {
        return $this->readValue($request, $annotation->getStartName(), $annotation->isRequired())
                    ->asDate($this->parseDate($annotation, 'defaultStart'),
                             new DateRange($this->parseDate($annotation, 'minStartDate'),
                                           $this->parseDate($annotation, 'maxStartDate')
                             )
        );
    }

    /**
     * retrieves start date
     *
     * @param   Request    $request
     * @param   string     $name
     * @return  Date
     */
    private function getEndDate(Request $request, Annotation $annotation)
    {
        return $this->readValue($request, $annotation->getEndName(), $annotation->isRequired())
                    ->asDate($this->parseDate($annotation, 'defaultEnd'),
                             new DateRange($this->parseDate($annotation, 'minEndDate'),
                                           $this->parseDate($annotation, 'maxEndDate')
                             )
        );
    }

    /**
     * handles single param
     *
     * @param   Request    $request
     * @param   string     $paramName
     * @parsm   bool       $required
     * @return  stubbles\input\filter\FilterValue
     */
    private function readValue(Request $request, $paramName, $required)
    {
        $valueFilter = $request->readParam($paramName);
        if ($required) {
            $valueFilter->required();
        }

        return $valueFilter;
    }

    /**
     * reads default value from annotation
     *
     * @param   Annotation $annotation
     * @return  Date
     */
    private function parseDate(Annotation $annotation, $field)
    {
        if ($annotation->hasValueByName($field)) {
            return new Date($annotation->getValueByName($field));
        }

        return null;
    }
}
