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
use stubbles\input\errors\ParamError;
use stubbles\input\errors\messages\LocalizedMessage;
use stubbles\lang;
use stubbles\lang\reflect\annotation\Annotation;
require_once __DIR__ . '/BrokerClass.php';
/**
 * Tests for stubbles\input\broker\RequestBrokerFacade.
 *
 * @since  2.0.0
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerFacadeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RequestBrokerFacade
     */
    private $requestBrokerFacade;
    /**
     * mocked request instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRequest;
    /**
     * mocked request broker
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRequestBroker;
    /**
     * mocked error messages
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockParamErrorMessages;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest            = $this->getMock('stubbles\input\Request');
        $this->mockRequestBroker      = $this->getMockBuilder('stubbles\input\broker\RequestBroker')
                                             ->disableOriginalConstructor()
                                             ->getMock();
        $this->mockParamErrorMessages = $this->getMock('stubbles\input\errors\messages\ParamErrorMessages');
        $this->requestBrokerFacade    = new RequestBrokerFacade($this->mockRequest,
                                                                $this->mockRequestBroker,
                                                                $this->mockParamErrorMessages
                                        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue(lang\reflect($this->requestBrokerFacade)->hasAnnotation('Singleton'));
    }

    /**
     * @test
     */
    public function procureWithoutErrorWriting()
    {
        $brokeredClass = new BrokerClass();
        $this->mockRequestBroker->expects($this->once())
                                ->method('procure')
                                ->with($this->equalTo($this->mockRequest),
                                       $this->equalTo($brokeredClass),
                                       $this->equalTo('main')
                                  );
        $this->mockRequest->expects($this->never())
                          ->method('paramErrors');
        $this->mockParamErrorMessages->expects($this->never())
                                     ->method('messageFor');
        $this->requestBrokerFacade->procure($brokeredClass, 'main');
    }

    /**
     * @test
     */
    public function writesNothingIfNoErrorsOccurred()
    {
        $brokeredClass = new BrokerClass();
        $this->mockRequestBroker->expects($this->once())
                                ->method('procure')
                                ->with($this->equalTo($this->mockRequest),
                                       $this->equalTo($brokeredClass),
                                       $this->equalTo(null)
                                  );
        $mockParamErrors = $this->getMock('stubbles\input\errors\ParamErrors');
        $this->mockRequest->expects($this->once())
                          ->method('paramErrors')
                          ->will($this->returnValue($mockParamErrors));
        $mockParamErrors->expects($this->once())
                        ->method('exist')
                        ->will($this->returnValue(false));
        $this->mockParamErrorMessages->expects($this->never())
                                     ->method('messageFor');
        $this->requestBrokerFacade->procure($brokeredClass, null, $this->expectWrite(null));
    }

    /**
     * @test
     */
    public function writesErrorMessageIfNoErrorsOccurred()
    {
        $brokeredClass = new BrokerClass();
        $this->mockRequestBroker->expects($this->once())
                                ->method('procure')
                                ->with($this->equalTo($this->mockRequest),
                                       $this->equalTo($brokeredClass),
                                       $this->equalTo(null)
                                  );
        $mockParamErrors = $this->getMock('stubbles\input\errors\ParamErrors');
        $this->mockRequest->expects($this->any())
                          ->method('paramErrors')
                          ->will($this->returnValue($mockParamErrors));
        $mockParamErrors->expects($this->once())
                        ->method('exist')
                        ->will($this->returnValue(true));
        $mockParamErrors->expects($this->once())
                        ->method('getIterator')
                        ->will($this->returnValue(new \ArrayIterator(['foo' => ['wth' => new ParamError('wth')]])));
        $this->mockParamErrorMessages->expects($this->once())
                                     ->method('messageFor')
                                     ->with()
                                     ->will($this->returnValue(new LocalizedMessage('en_EN', 'Error, dude!')));
        $this->requestBrokerFacade->procure($brokeredClass, null, $this->expectWrite('foo: Error, dude!'));
    }

    /**
     * creates a write function
     *
     * @param   string  $expect
     * @return  Closure
     */
    private function expectWrite($expect)
    {
        $mockOutputStream = $this->getMock('stubbles\streams\OutputStream');
        if (null === $expect) {
            $mockOutputStream->expects($this->never())
                             ->method('writeLine');
        } else {
            $mockOutputStream->expects($this->once())
                             ->method('writeLine')
                             ->with($this->equalTo($expect));
        }

        return function($paramName, $message) use($mockOutputStream)
               {
                   $mockOutputStream->writeLine($paramName . ': ' . $message);
               };
    }

    /**
     * @test
     */
    public function getMethodsReturnsListOfAllMethodsWithRequestAnnotation()
    {
        $brokeredClass = new BrokerClass();
        $expected      = [lang\reflect($brokeredClass, 'setBar')];
        $this->mockRequestBroker->expects($this->once())
                                ->method('getMethods')
                                ->with($this->equalTo($brokeredClass),
                                       $this->equalTo('main')
                                  )
                                ->will($this->returnValue($expected));
        $this->assertEquals($expected,
                            $this->requestBrokerFacade->getMethods($brokeredClass, 'main')
        );
    }

    /**
     * @test
     */
    public function getAnnotationsReturnsListOfAllRequestAnnotation()
    {
        $brokeredClass = new BrokerClass();
        $expected      = [new Annotation('Test')];
        $this->mockRequestBroker->expects($this->once())
                                ->method('getAnnotations')
                                ->with($this->equalTo($brokeredClass),
                                       $this->equalTo('main')
                                  )
                                ->will($this->returnValue($expected));
        $this->assertEquals($expected,
                            $this->requestBrokerFacade->getAnnotations($brokeredClass, 'main')
        );
    }
}
