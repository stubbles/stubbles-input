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
 * Tests for net\stubbles\input\broker\RequestBrokerMethods.
 *
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerMethodsTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RequestBrokerMethods
     */
    private $requestBrokerMethods;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->requestBrokerMethods = new RequestBrokerMethods();
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue($this->requestBrokerMethods->getClass()
                                                     ->hasAnnotation('Singleton')
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function getMethodsOnNonObjectThrowsIllegalArgumentException()
    {
        $this->requestBrokerMethods->get('foo');
    }

    /**
     * @test
     */
    public function getReturnsListOfAllMethodsWithRequestAnnotation()
    {
        $methods = $this->requestBrokerMethods->get(new BrokerClass());
        $this->assertCount(2, $methods);
        foreach ($methods as $method) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionMethod',
                                    $method
            );
        }
    }

    /**
     * @test
     */
    public function getAnnotationsReturnsListOfAllRequestAnnotation()
    {
        $annotations = $this->requestBrokerMethods->getAnnotations(new BrokerClass());
        $this->assertCount(2, $annotations);
        foreach ($annotations as $annotation) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                    $annotation
            );
        }
    }
}
?>