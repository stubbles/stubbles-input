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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker to be used to retrieve parameters based on annotations.
 */
abstract class MultipleSourceParamBroker extends BaseObject implements ParamBroker
{
    /**
     * retrieves method to call on request instance
     *
     * @param   Request     $request
     * @param   Annotation  $annotation
     * @param   string      $type
     * @return  string
     * @throws  RuntimeException
     */
    protected function getMethod(Request $request, Annotation $annotation, $type)
    {
        $method = $type . $this->getSource($annotation);
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
    protected function getSource(Annotation $annotation)
    {
        if ($annotation->hasValueByName('source')) {
            return ucfirst(strtolower($annotation->getSource()));
        }

        return 'Param';
    }
}
?>