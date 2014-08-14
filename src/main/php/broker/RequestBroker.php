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
     * @type  \stubbles\input\broker\RequestBrokerMethods
     */
    private $brokerMethods;
    /**
     * factory to create filters with
     *
     * @type  \stubbles\input\broker\ParamBrokers
     */
    private $paramBrokers;

    /**
     * constructor
     *
     * @param  \stubbles\input\broker\RequestBrokerMethods  $brokerMethods
     * @param  \stubbles\input\broker\ParamBrokers          $paramBrokers
     * @Inject
     */
    public function __construct(RequestBrokerMethods $brokerMethods, ParamBrokers $paramBrokers)
    {
        $this->brokerMethods = $brokerMethods;
        $this->paramBrokers  = $paramBrokers;
    }

    /**
     * fills given object with values from request
     *
     * @param   \stubbles\input\Request  $request
     * @param   object                   $object   the object instance to fill with values
     * @param   string                   $group    restrict procurement to given group
     */
    public function procure(Request $request, $object, $group = null)
    {
        foreach ($this->brokerMethods->of($object, $group) as $refMethod) {
            $value = $this->paramBrokers->procure($request, $refMethod->annotation('Request'));
            if (null !== $value) {
                $refMethod->invoke($object, $value);
            }
        }
    }

    /**
     * returns a list of all request annotations on given object
     *
     * @param   object  $object
     * @param   string  $group   restrict list to given group
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    public function annotationsFor($object, $group = null)
    {
        return $this->brokerMethods->annotationsFor($object, $group);
    }
}
