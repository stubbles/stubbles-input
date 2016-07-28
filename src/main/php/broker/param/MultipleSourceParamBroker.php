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
namespace stubbles\input\broker\param;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\reflect\annotation\Annotation;
use stubbles\values\Value;
/**
 * Broker to be used to retrieve parameters based on annotations.
 */
abstract class MultipleSourceParamBroker implements ParamBroker
{
    /**
     * extracts parameter from request and handles it
     *
     * @param   \stubbles\input\Request                  $request     instance to handle value with
     * @param   \stubbles\reflect\annotation\Annotation  $annotation  annotation which contains request param metadata
     * @return  mixed
     */
    public function procure(Request $request, Annotation $annotation)
    {
        $read        = $this->readSourceMethod($request, $annotation);
        $valueReader = $request->$read($annotation->getParamName());
        /* @var $valueReader \stubbles\input\ValueReader */
        if ($annotation->isRequired()) {
            return $this->filter(
                    $valueReader->required($annotation->getRequiredErrorId('FIELD_EMPTY')),
                    $annotation
            );
        }

        if ($this->supportsDefault() && $annotation->hasValueByName('default')) {
            return $this->filter(
                    $valueReader->defaultingTo($this->parseDefault($annotation->getDefault())),
                    $annotation
            );
        }

        return $this->filter($valueReader, $annotation);
    }

    /**
     * whether a default value for this param is supported
     *
     * @return  bool
     */
    protected function supportsDefault(): bool
    {
        return true;
    }

    /**
     * parses default value from annotation
     *
     * @param   string  $value
     * @return  mixed
     */
    protected function parseDefault($value)
    {
        return $value;
    }

    /**
     * handles a single param
     *
     * @param   \stubbles\values\Value                   $param
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @return  mixed
     */
    public function procureParam(Param $param, Annotation $annotation)
    {
        return $this->filter(ValueReader::forValue($param->value()), $annotation);
    }

    /**
     * retrieves method to call on request instance
     *
     * @param   \stubbles\input\Request                  $request
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @return  string
     * @throws  \RuntimeException
     */
    private function readSourceMethod(Request $request, Annotation $annotation): string
    {
        $method = 'read' . $this->source($annotation);
        if (!method_exists($request, $method)) {
            throw new \RuntimeException(
                    'Unknown source ' . $annotation->getSource() . ' for '
                    . $annotation . ' on ' . get_class($request)
            );
        }

        return $method;
    }

    /**
     * returns source from where to read value
     *
     * @param   \stubbles\reflect\annotation\Annotation  $annotation
     * @return  string
     */
    private function source(Annotation $annotation): string
    {
        if ($annotation->hasValueByName('source')) {
            return ucfirst(strtolower($annotation->getSource()));
        }

        return 'Param';
    }

    /**
     * filters single param
     *
     * @param   \stubbles\input\valuereader\CommonValueReader  $valueReader  instance to filter value with
     * @param   \stubbles\reflect\annotation\Annotation   $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected abstract function filter(CommonValueReader $valueReader, Annotation $annotation);
}
