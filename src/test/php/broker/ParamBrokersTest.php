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
use stubbles\lang\reflect;
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
        $annotations = reflect\annotationsOfConstructor($this->requestBroker);
        $this->assertTrue($annotations->contain('Inject'));
        $this->assertTrue($annotations->contain('Map'));
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
        $this->assertInstanceOf(
                $brokerClass,
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
        $this->assertInstanceOf(
                $brokerClass,
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
        $requestBroker = new RequestBroker(['mock' => $mockParamBroker]);
        $this->assertInstanceOf(
                $brokerClass,
                $requestBroker->paramBroker($key)
        );
    }

    /**
     * @test
     */
    public function returnsAddedBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $requestBroker = new RequestBroker(['Mock' => $mockParamBroker]);
        $this->assertSame(
                $mockParamBroker,
                $requestBroker->paramBroker('mock')
        );
    }

    /**
     * @test
     */
    public function canOverwriteDefaultBroker()
    {
        $mockParamBroker = $this->getMock('stubbles\input\broker\param\ParamBroker');
        $requestBroker = new RequestBroker(['string' => $mockParamBroker]);
        $this->assertSame(
                $mockParamBroker,
                $requestBroker->paramBroker('string')
        );
    }
}
