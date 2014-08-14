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
     * @type  \stubbles\input\Request
     */
    private $request;
    /**
     * factory to create filters with
     *
     * @type  \stubbles\input\broker\RequestBroker
     */
    private $requestBroker;
    /**
     * access to error messages
     *
     * @type  \stubbles\input\errors\ParamErrorMessages
     */
    private $errorMessages;

    /**
     * constructor
     *
     * @param  \stubbles\input\Request                    $request
     * @param  \stubbles\input\broker\RequestBroker       $requestBroker
     * @param  \stubbles\input\errors\ParamErrorMessages  $errorMessages
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
     * @param  callable  $write   function to call when errors should be processed
     */
    public function procure($object, $group = null, callable $write = null)
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
}
