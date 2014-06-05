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
use stubbles\input\errors\messages\ParamErrorMessages;
/**
 * Broker class to transfer values from the request into an object via annotations.
 *
 * @since  2.0.0
 * @Singleton
 */
class RequestBrokerFacade
{
    /**
     * request instance
     *
     * @type  Request
     */
    private $request;
    /**
     * factory to create filters with
     *
     * @type  RequestBroker
     */
    private $requestBroker;
    /**
     * access to error messages
     *
     * @type  ParamErrorMessages
     */
    private $errorMessages;

    /**
     * constructor
     *
     * @param  Request             $request
     * @param  RequestBroker       $requestBroker
     * @param  ParamErrorMessages  $errorMessages
     * @Inject
     */
    public function __construct(Request $request, RequestBroker $requestBroker, ParamErrorMessages $errorMessages)
    {
        $this->request       = $request;
        $this->requestBroker = $requestBroker;
        $this->errorMessages = $errorMessages;
    }

    /**
     * fills given object with values from request
     *
     * @param  object    $object
     * @param  string    $group
     * @param  \Closure  $write   function to call when errors should be processed
     */
    public function procure($object, $group = null, \Closure $write = null)
    {
        $this->requestBroker->procure($this->request, $object, $group);
        if (null === $write || !$this->request->paramErrors()->exist()) {
            return;
        }

        foreach ($this->request->paramErrors() as $paramName => $errors) {
            foreach ($errors as $error) {
                $write($paramName, $this->errorMessages->messageFor($error));
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
        return $this->requestBroker->getMethods($object, $group);
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
        return $this->requestBroker->getAnnotations($object, $group);
    }
}
