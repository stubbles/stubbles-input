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
use net\stubbles\input\filter\ValueFilter;
use org\stubbles\input\test\BrokerClass;
/**
 * Tests for net\stubbles\input\broker\RequestBroker.
 *
 * @group  broker
 */
class RequestBrokerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RequestBroker
     */
    private $requestBroker;
    /**
     * mocked request instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->requestBroker = new RequestBroker();
        $this->mockRequest   = $this->getMock('net\\stubbles\\input\\Request');
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue($this->requestBroker->getClass()->hasAnnotation('Singleton'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetParamBrokerMethod()
    {
        $setParamBroker = $this->requestBroker->getClass()->getMethod('setParamBroker');
        $this->assertTrue($setParamBroker->hasAnnotation('Inject'));
        $this->assertTrue($setParamBroker->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setParamBroker->hasAnnotation('Map'));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function processNonObjectThrowsIllegalArgumentException()
    {
        $this->requestBroker->process($this->mockRequest, 'foo');
    }

    /**
     * @test
     */
    public function processesOnlyThoseInGivenGroup()
    {
        $this->mockRequest->expects($this->once())
                          ->method('filterParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue(ValueFilter::mockForValue('just some string value')));
        $object = new BrokerClass();
        $this->requestBroker->process($this->mockRequest, $object, 'main');
        $this->assertEquals('just some string value', $object->getBar());
        $this->assertNull($object->getBaz());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function processesWithUnknownFilterAnnotationThrowsRuntimeException()
    {
        $this->mockRequest->expects($this->any())
                          ->method('filterParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue(ValueFilter::mockForValue('just some string value')));
        $this->requestBroker->process($this->mockRequest, new BrokerClass());
    }

    /**
     * @test
     */
    public function processesWithUserDefinedFilterAnnotation()
    {
        $this->mockRequest->expects($this->once())
                          ->method('filterParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue(ValueFilter::mockForValue('just some string value')));
        $mockParamBroker = $this->getMock('net\\stubbles\\input\\broker\\param\\ParamBroker');
        $mockParamBroker->expects($this->once())
                        ->method('handle')
                        ->will($this->returnValue('just another string value'));
        $object = new BrokerClass();
        $this->requestBroker->setParamBroker(array('MockFilter' => $mockParamBroker))
                            ->process($this->mockRequest, $object);
        $this->assertEquals('just some string value', $object->getBar());
        $this->assertEquals('just another string value', $object->getBaz());
    }
}
?>