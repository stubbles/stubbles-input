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
use stubbles\input\ValueReader;
use stubbles\input\valuereader\CommonValueReader;
use stubbles\lang\exception\RuntimeException;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker to be used to retrieve parameters based on annotations.
 */
abstract class MultipleSourceParamBroker implements ParamBroker
{
    /**
     * extracts parameter from request and handles it
     *
     * @param   Request     $request     instance to handle value with
     * @param   Annotation  $annotation  annotation which contains request param metadata
     * @return  mixed
     */
    public function procure(Request $request, Annotation $annotation)
    {
        $method      = $this->getMethod($request, $annotation);
        $valueReader = $request->$method($annotation->getName());
        /* @var $valueReader \stubbles\input\ValueReader */
        if ($annotation->isRequired()) {
            return $this->filter($valueReader->required($annotation->getRequiredErrorId('FIELD_EMPTY')), $annotation);
        }

        if ($this->supportsDefault() && $annotation->hasValueByName('default')) {
            return $this->filter($valueReader->defaultingTo($this->parseDefault($annotation->getDefault())), $annotation);
        }

        return $this->filter($valueReader, $annotation);
    }

    /**
     * whether a default value for this param is supported
     *
     * @return  bool
     */
    protected function supportsDefault()
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
     * @param   Param       $param
     * @param   Annotation  $annotation
     * @return  mixed
     */
    public function procureParam(Param $param, Annotation $annotation)
    {
        return $this->filter(ValueReader::forParam($param), $annotation);
    }

    /**
     * retrieves method to call on request instance
     *
     * @param   Request     $request
     * @param   Annotation  $annotation
     * @return  string
     * @throws  RuntimeException
     */
    private function getMethod(Request $request, Annotation $annotation)
    {
        $method = 'read' . $this->getSource($annotation);
        if (!method_exists($request, $method)) {
            throw new RuntimeException('Unknown source ' . $annotation->getSource() . ' for ' . $annotation . ' on ' . get_class($request));
        }

        return $method;
    }

    /**
     * returns source from where to read value
     *
     * @param   Annotation  $annotation
     * @return  string
     */
    private function getSource(Annotation $annotation)
    {
        if ($annotation->hasValueByName('source')) {
            return ucfirst(strtolower($annotation->getSource()));
        }

        return 'Param';
    }

    /**
     * filters single param
     *
     * @param   CommonValueReader  $valueReader  instance to filter value with
     * @param   Annotation         $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected abstract function filter(CommonValueReader $valueReader, Annotation $annotation);
}
