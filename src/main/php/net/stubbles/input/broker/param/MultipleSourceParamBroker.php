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
use net\stubbles\input\filter\ValueFilter;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker to be used to filter parameters based on annotations.
 */
abstract class MultipleSourceParamBroker extends BaseObject implements ParamBroker
{
    /**
     * handles single param
     *
     * @param   Request      $request     instance to handle value with
     * @param   Annotation   $annotation  annotation which contains filter metadata
     * @return  mixed
     * @throws  RuntimeException
     */
    public function handle(Request $request, Annotation $annotation)
    {
        $method = 'filter' . $this->getSource($annotation);
        if (!method_exists($request, $method)) {
            throw new RuntimeException('Unknown source ' . $annotation->getSource() . ' for ' . $annotation . ' on ' . $request->getClassName());
        }

        return $this->filter($request->$method($annotation->getFieldName()), $annotation);
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
     * @param   ValueFilter  $valueFilter  instance to filter value with
     * @param   Annotation   $annotation   annotation which contains filter metadata
     * @return  mixed
     */
    protected abstract function filter(ValueFilter $valueFilter, Annotation $annotation);
}
?>