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
require_once __DIR__ . '/BrokerClass.php';
/**
 * Tests for stubbles\input\broker\RequestBrokerMethods.
 *
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerMethodsTest extends \PHPUnit_Framework_TestCase
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
        $this->assertTrue(lang\reflect($this->requestBrokerMethods)->hasAnnotation('Singleton'));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function getMethodsOnNonObjectThrowsInvalidArgumentException()
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
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionMethod',
                                    $method
            );
        }
    }

    /**
     * @test
     */
    public function getReturnsListOfAllMethodsWithRequestAnnotationOnClassName()
    {
        $methods = $this->requestBrokerMethods->get('stubbles\input\broker\BrokerClass');
        $this->assertCount(3, $methods);
        foreach ($methods as $method) {
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionMethod',
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
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionMethod',
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
            $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
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
            $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                    $annotation
            );
        }
    }

    /**
     * @test
     */
    public function getAnnotationsReturnsListOfAllRequestAnnotationInGivenGroupOnClassName()
    {
        $annotations = $this->requestBrokerMethods->getAnnotations('stubbles\input\broker\BrokerClass',
                                                                   'main'
        );
        $this->assertCount(1, $annotations);
        foreach ($annotations as $annotation) {
            $this->assertInstanceOf('stubbles\lang\\reflect\annotation\Annotation',
                                    $annotation
            );
        }
    }
}
