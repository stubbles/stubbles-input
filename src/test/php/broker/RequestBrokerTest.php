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
use stubbles\input\ValueReader;
use stubbles\lang;
require_once __DIR__ . '/BrokerClass.php';
/**
 * Tests for stubbles\input\broker\RequestBroker.
 *
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerTest extends \PHPUnit_Framework_TestCase
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
        $this->mockRequest   = $this->getMock('stubbles\input\Request');
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue(
                lang\reflect($this->requestBroker)->hasAnnotation('Singleton')
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function procureNonObjectThrowsInvalidArgumentException()
    {
        $this->requestBroker->procure($this->mockRequest, 313);
    }

    /**
     * @test
     */
    public function procuresOnlyThoseInGivenGroup()
    {
        $object = new BrokerClass();
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue(ValueReader::forValue('just some string value')));
        $this->requestBroker->procure($this->mockRequest, $object, 'main');
        $this->assertFalse($object->isVerbose());
        $this->assertEquals('just some string value', $object->getBar());
        $this->assertNull($object->getBaz());
    }

    /**
     * @test
     */
    public function procuresAllIfNoGroupGiven()
    {

        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $mockParamBroker->expects($this->once())
                        ->method('procure')
                        ->will($this->returnValue('just another string value'));
        $this->mockRequest->expects($this->any())
                          ->method('readParam')
                          ->will($this->onConsecutiveCalls(
                                  ValueReader::forValue('on'),
                                  ValueReader::forValue('just some string value'),
                                  ValueReader::forValue('just another string value')
                                 )
                            );
        $object = new BrokerClass();
        $requestBroker = new RequestBroker(['Mock' => $mockParamBroker]);
        $requestBroker->procure($this->mockRequest, $object);
        $this->assertTrue($object->isVerbose());
        $this->assertEquals('just some string value', $object->getBar());
        $this->assertEquals('just another string value', $object->getBaz());
    }
}
