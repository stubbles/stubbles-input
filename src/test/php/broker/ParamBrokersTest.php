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
/**
 * Tests for stubbles\input\broker\RequestBroker.
 *
 * @group  broker
 * @group  broker_core
 */
class ParamBrokersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\broker\RequestBroker
     */
    private $requestBroker;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->requestBroker = new RequestBroker();
    }

    /**
     * @test
     */
    public function annotationsPresentOnAddParamBrokersMethod()
    {
        $addParamBrokersMethod = lang\reflect($this->requestBroker, 'addParamBrokers');
        $this->assertTrue($addParamBrokersMethod->hasAnnotation('Inject'));
        $this->assertTrue($addParamBrokersMethod->annotation('Inject')->isOptional());
        $this->assertTrue($addParamBrokersMethod->hasAnnotation('Map'));
    }

    /**
     * returns default broker list
     *
     * @return  array
     */
    public function defaultBrokerList()
    {
        $defaultBroker = [];
        foreach (RequestBroker::buildInTypes() as $name => $paramBroker) {
            $defaultBroker[] = [$name, get_class($paramBroker)];
        }

        return $defaultBroker;
    }

    /**
     * @test
     * @dataProvider  defaultBrokerList
     */
    public function returnsBroker($key, $brokerClass)
    {
        $this->assertInstanceOf($brokerClass,
                                $this->requestBroker->paramBroker($key)
        );
    }

    /**
     * @test
     * @dataProvider  defaultBrokerList
     * @since  2.3.3
     * @group  issue_45
     */
    public function returnsBrokerWithLowerCaseKey($key, $brokerClass)
    {
        $this->assertInstanceOf($brokerClass,
                                $this->requestBroker->paramBroker(strtolower($key))
        );
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function requestUnknownParamBrokerTypeThrowsRuntimeException()
    {
        $this->requestBroker->paramBroker('doesNotExist');
    }

    /**
     * @test
     * @dataProvider  defaultBrokerList
     */
    public function addingBrokersDoesNotOverrideDefaultBrokers($key, $brokerClass)
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertInstanceOf(
                $brokerClass,
                $this->requestBroker->addParamBrokers(['mock' => $mockParamBroker])
                ->paramBroker($key)
        );
    }

    /**
     * @test
     */
    public function returnsAddedBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertSame(
                $mockParamBroker,
                $this->requestBroker->addParamBrokers(['Mock' => $mockParamBroker])
                                    ->paramBroker('mock')
        );
    }

    /**
     * @test
     */
    public function canOverwriteDefaultBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $this->assertSame(
                $mockParamBroker,
                $this->requestBroker->addParamBrokers(['string' => $mockParamBroker])
                                    ->paramBroker('string')
        );
    }
}
