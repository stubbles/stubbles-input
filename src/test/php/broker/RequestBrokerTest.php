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
use stubbles\lang;
use stubbles\lang\reflect\annotation\Annotation;
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
     * mocked param broker map
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockParamBrokers;
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
        $this->mockParamBrokers = $this->getMockBuilder('stubbles\input\broker\ParamBrokers')
                                       ->disableOriginalConstructor()
                                       ->getMock();
        $this->requestBroker = new RequestBroker(new RequestBrokerMethods(), $this->mockParamBrokers);
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
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue(
                lang\reflectConstructor($this->requestBroker)->hasAnnotation('Inject')
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
        $this->mockParamBrokers->expects($this->once())
                               ->method('procure')
                               ->with(
                                        $this->equalTo($this->mockRequest),
                                        $this->equalTo(
                                                new Annotation(
                                                        'String',
                                                        'stubbles\input\broker\BrokerClass::setBar()',
                                                        ['name' => 'bar', 'group' => 'main'],
                                                        'Request'
                                                )
                                        )
                                 )
                               ->will($this->returnValue('just some string value'));
        $object = new BrokerClass();
        $this->requestBroker->procure($this->mockRequest, $object, 'main');
        $this->assertEquals('just some string value', $object->getBar());
        $this->assertNull($object->getBaz());
    }

    /**
     * @test
     */
    public function procuresAllIfNoGroupGiven()
    {
        $this->mockParamBrokers->expects($this->at(0))
                               ->method('procure')
                               ->with(
                                        $this->equalTo($this->mockRequest),
                                        $this->equalTo(
                                                new Annotation(
                                                        'Bool',
                                                        'stubbles\input\broker\BrokerClass::enableVerbose()',
                                                        ['name' => 'verbose', 'group' => 'noparam'],
                                                        'Request'
                                                )
                                        )
                                 )
                               ->will($this->returnValue('just some string value'));
        $this->mockParamBrokers->expects($this->at(1))
                               ->method('procure')
                               ->with(
                                        $this->equalTo($this->mockRequest),
                                        $this->equalTo(
                                                new Annotation(
                                                        'String',
                                                        'stubbles\input\broker\BrokerClass::setBar()',
                                                        ['name' => 'bar', 'group' => 'main'],
                                                        'Request'
                                                )
                                        )
                                 )
                               ->will($this->returnValue('just some string value'));
        $this->mockParamBrokers->expects($this->at(2))
                               ->method('procure')
                               ->with(
                                        $this->equalTo($this->mockRequest),
                                        $this->equalTo(
                                                new Annotation(
                                                        'Mock',
                                                        'stubbles\input\broker\BrokerClass::setBaz()',
                                                        ['name' => 'baz', 'group' => 'other'],
                                                        'Request'
                                                )
                                        )
                                 )
                               ->will($this->returnValue('just another string value'));

        $object = new BrokerClass();
        $this->requestBroker->procure($this->mockRequest, $object);
        $this->assertEquals('just some string value', $object->getBar());
        $this->assertEquals('just another string value', $object->getBaz());
    }

    /**
     * @test
     */
    public function annotationsForReturnsListOfAllRequestAnnotation()
    {
        $this->assertCount(3, $this->requestBroker->annotationsFor(new BrokerClass()));
    }

    /**
     * @test
     */
    public function annotationsForReturnsListOfAllRequestAnnotationInGivenGroup()
    {
        $annotations = $this->requestBroker->annotationsFor(new BrokerClass(), 'main');
        $this->assertCount(1, $annotations);
        foreach ($annotations as $annotation) {
            $this->assertEquals('main', $annotation->group());
        }
    }
}
