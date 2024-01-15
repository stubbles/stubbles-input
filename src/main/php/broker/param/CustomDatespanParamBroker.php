<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use Override;
use stubbles\date\Date;
use stubbles\date\span\CustomDatespan;
use stubbles\input\Request;
use stubbles\input\filter\range\DateRange;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
/**
 * Filter parameters based on a @Request[CustomDatespan] annotation.
 */
class CustomDatespanParamBroker implements ParamBroker
{
    #[Override]
    public function procure(Request $request, Annotation $annotation): ?CustomDatespan
    {
        $startDate = $this->getDate($request, $annotation, 'Start');
        $endDate   = $this->getDate($request, $annotation, 'End');
        if (null !== $startDate && null !== $endDate) {
            return new CustomDatespan($startDate, $endDate);
        }

        return null;
    }

    private function getDate(Request $request, Annotation $annotation, string $type): ?Date
    {
        $nameMethod = 'get' . $type . 'Name';
        return $this->readValue(
            $request,
            $annotation->$nameMethod(),
            $annotation->isRequired(),
            $this->parseDate($annotation, 'default' . $type)
        )->asDate(new DateRange(
            $this->parseDate($annotation, "min{$type}Date"),
            $this->parseDate($annotation, "max{$type}Date")
        )
        );
    }

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

    private function parseDate(Annotation $annotation, string $field): ?Date
    {
        if ($annotation->hasValueByName($field)) {
            return new Date($annotation->getValueByName($field));
        }

        return null;
    }
}
