<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker;
use stubbles\input\Request;
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
 * @Singleton
 */
class RequestBroker
{
    /**
     * the matcher to be used for methods and properties
     *
     * @type  RequestBrokerMethods
     */
    private $brokerMethods;
    /**
     * factory to create filters with
     *
     * @type  ParamBrokerMap
     */
    private $paramBrokerMap;

    /**
     * constructor
     *
     * @param  RequestBrokerMethods  $brokerMethods
     * @param  ParamBrokerMap        $paramBrokerMap
     * @Inject
     */
    public function __construct(RequestBrokerMethods $brokerMethods, ParamBrokerMap $paramBrokerMap)
    {
        $this->brokerMethods  = $brokerMethods;
        $this->paramBrokerMap = $paramBrokerMap;
    }

    /**
     * fills given object with values from request
     *
     * @param   Request  $request
     * @param   object   $object   the object instance to fill with values
     * @param   string   $group    restrict procurement to given group
     */
    public function procure(Request $request, $object, $group = null)
    {
        foreach ($this->brokerMethods->get($object, $group) as $refMethod) {
            $requestAnnotation = $refMethod->getAnnotation('Request');
            $value = $this->paramBrokerMap->getBroker($requestAnnotation->getAnnotationName())
                                          ->procure($request, $requestAnnotation);
            if (null !== $value) {
                $refMethod->invoke($object, $value);
            }
        }
    }

    /**
     * returns all methods of given instance which are applicable for brokerage
     *
     * @param   object  $object
     * @param   string  $group   restrict list to given group
     * @return  ReflectionMethod[]
     */
    public function getMethods($object, $group = null)
    {
        return $this->brokerMethods->get($object, $group);
    }

    /**
     * returns a list of all request annotations on given object
     *
     * @param   object  $object
     * @param   string  $group   restrict list to given group
     * @return  stubbles\lang\reflect\annotation\Annotation[]
     */
    public function getAnnotations($object, $group = null)
    {
        return $this->brokerMethods->getAnnotations($object, $group);
    }
}
