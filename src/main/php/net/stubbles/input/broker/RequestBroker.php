<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker;
use net\stubbles\input\Request;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
 * @Singleton
 */
class RequestBroker extends BaseObject
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
     * @param   string   $group    group of values to filter
     */
    public function procure(Request $request, $object, $group = null)
    {
        foreach ($this->brokerMethods->get($object) as $refMethod) {
            $requestAnnotation = $refMethod->getAnnotation('Request');
            if ($this->isNotInGroup($group, $requestAnnotation)) {
                continue;
            }

            $value = $this->paramBrokerMap->getBroker($requestAnnotation->getAnnotationName())
                                          ->procure($request, $requestAnnotation);
            if (null !== $value) {
                $refMethod->invoke($object, $value);
            }
        }
    }

    /**
     * checks whether the annotation belongs to the given group
     *
     * @param   string      $group
     * @param   Annotation  $requestAnnotation
     * @return  bool
     */
    private function isNotInGroup($group, Annotation $requestAnnotation)
    {
        if (empty($group)) {
            return false;
        }

        return $requestAnnotation->getGroup() !== $group;
    }

    /**
     * returns all methods of given instance which are applicable for brokerage
     *
     * @param   object $object
     * @return  ReflectionMethod[]
     */
    public function getMethods($object)
    {
        return $this->brokerMethods->get($object);
    }

    /**
     * returns a list of all request annotations on given object
     *
     * @param   object  $object
     * @return  net\stubbles\lang\reflect\annotation\Annotation[]
     */
    public function getAnnotations($object)
    {
        return $this->brokerMethods->getAnnotations($object);
    }
}
?>