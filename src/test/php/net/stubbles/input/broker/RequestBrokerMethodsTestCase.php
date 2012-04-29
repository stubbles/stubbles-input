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
        $this->requestBrokerMethods->get(303);
    }

    /**
     * @test
     */
    public function getReturnsListOfAllMethodsWithRequestAnnotation()
    {
        $methods = $this->requestBrokerMethods->get(new BrokerClass());
        $this->assertCount(3, $methods);
        foreach ($methods as $method) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionMethod',
                                    $method
            );
        }
    }

    /**
     * @test
     */
    public function getReturnsListOfAllMethodsWithRequestAnnotationOnClassName()
    {
        $methods = $this->requestBrokerMethods->get('org\\stubbles\\input\\test\\BrokerClass');
        $this->assertCount(3, $methods);
        foreach ($methods as $method) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionMethod',
                                    $method
            );
        }
    }

    /**
     * @test
     */
    public function getReturnsListOfAllMethodsWithRequestAnnotationInGivenGroup()
    {
        $methods = $this->requestBrokerMethods->get(new BrokerClass(), 'main');
        $this->assertCount(1, $methods);
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
        $this->assertCount(3, $annotations);
        foreach ($annotations as $annotation) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                    $annotation
            );
        }
    }

    /**
     * @test
     */
    public function getAnnotationsReturnsListOfAllRequestAnnotationInGivenGroup()
    {
        $annotations = $this->requestBrokerMethods->getAnnotations(new BrokerClass(), 'main');
        $this->assertCount(1, $annotations);
        foreach ($annotations as $annotation) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                    $annotation
            );
            $this->assertTrue($annotation->requiresValue());
        }
    }

    /**
     * @test
     */
    public function getAnnotationsReturnsListOfAllRequestAnnotationInGivenGroupOnClassName()
    {
        $annotations = $this->requestBrokerMethods->getAnnotations('org\\stubbles\\input\\test\\BrokerClass',
                                                                   'main'
        );
        $this->assertCount(1, $annotations);
        foreach ($annotations as $annotation) {
            $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\annotation\\Annotation',
                                    $annotation
            );
            $this->assertTrue($annotation->requiresValue());
        }
    }

    /**
     * @test
     */
    public function getAnnotationsDoesNotSetRequiresValueForMethodsWithoutParameters()
    {
        $annotations = $this->requestBrokerMethods->getAnnotations('org\\stubbles\\input\\test\\BrokerClass',
                                                                   'noparam'
        );
        $this->assertCount(1, $annotations);
        foreach ($annotations as $annotation) {
            $this->assertFalse($annotation->requiresValue());
        }
    }
}
?>