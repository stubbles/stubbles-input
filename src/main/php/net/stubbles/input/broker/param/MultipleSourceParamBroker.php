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
use net\stubbles\input\Param;
use net\stubbles\input\Request;
use net\stubbles\input\ValueReader;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker to be used to retrieve parameters based on annotations.
 */
abstract class MultipleSourceParamBroker extends BaseObject implements ParamBroker
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
        if ($annotation->isRequired()) {
            $valueReader->required($annotation->getRequiredErrorId('FIELD_EMPTY'));
        }

        return $this->filter($valueReader, $annotation);
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
            throw new RuntimeException('Unknown source ' . $annotation->getSource() . ' for ' . $annotation . ' on ' . $request->getClassName());
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
     * @param   ValueReader  $valueReader  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected abstract function filter(ValueReader $valueReader, Annotation $annotation);
}
?>