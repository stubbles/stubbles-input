<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\date\Date;
use stubbles\date\span\CustomDatespan;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\input\filter\range\DateRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter parameters based on a @Request[CustomDatespan] annotation.
 */
class CustomDatespanParamBroker implements ParamBroker
{
    /**
     * handles single param
     *
     * @param   \stubbles\input\Request                  $request     instance to handle value with
     * @param   \stubbles\reflect\annotation\Annotation  $annotation  annotation which contains request param metadata
     * @return  \stubbles\date\span\CustomDatespan
     */
    public function procure(Request $request, Annotation $annotation): ?CustomDatespan
    {
        $startDate = $this->getDate($request, $annotation, 'Start');
        $endDate   = $this->getDate($request, $annotation, 'End');
        if (null !== $startDate && null !== $endDate) {
            return new CustomDatespan($startDate, $endDate);
        }

        return null;
    }

    /**
     * handles a single param
     *
     * @param   \stubbles\input\Param                         $param
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @return  mixed
     */
    public function procureParam(Param $param, Annotation $annotation)
    {
        throw new \BadMethodCallException('Can not procure a single param');
    }

    /**
     * retrieves start date
     *
     * @param   \stubbles\input\Request                       $request
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @param   string      $type
     * @return  \stubbles\date\Date
     */
    private function getDate(Request $request, Annotation $annotation, $type): ?Date
    {
        $nameMethod = 'get' . $type . 'Name';
        return $this->readValue(
                        $request,
                        $annotation->$nameMethod(),
                        $annotation->isRequired(),
                        $this->parseDate($annotation, 'default' . $type)
                )
                ->asDate(new DateRange(
                        $this->parseDate($annotation, "min{$type}Date"),
                        $this->parseDate($annotation, "max{$type}Date")
                )
        );
    }

    /**
     * handles single param
     *
     * @param   \stubbles\input\Request  $request
     * @param   string                   $paramName
     * @param   bool                     $required
     * @return  \stubbles\input\valuereader\CommonValueReader
     */
    private function readValue(
            Request $request,
            string $paramName,
            bool $required,
            Date $default = null
    ): CommonValueReader {
        $valueFilter = $request->readParam($paramName);
        if ($required) {
            return $valueFilter->required();
        } elseif (null !== $default) {
            return $valueFilter->defaultingTo($default);
        }

        return $valueFilter;
    }

    /**
     * reads default value from annotation
     *
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @param   string                                        $field
     * @return  \stubbles\date\Date
     */
    private function parseDate(Annotation $annotation, string $field): ?Date
    {
        if ($annotation->hasValueByName($field)) {
            return new Date($annotation->getValueByName($field));
        }

        return null;
    }
}
