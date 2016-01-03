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
use bovigo\callmap\NewInstance;
use stubbles\input\broker\param\ParamBroker;

use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
use function stubbles\reflect\annotationsOfConstructor;
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
        assertTrue(
                annotationsOfConstructor($this->requestBroker)->contain('Map')
        );
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
        assert(
                $this->requestBroker->paramBroker($key),
                isInstanceOf($brokerClass)
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
        assert(
                $this->requestBroker->paramBroker(strtolower($key)),
                isInstanceOf($brokerClass)
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
        $oaramBroker   = NewInstance::of(ParamBroker::class);
        $requestBroker = new RequestBroker(['mock' => $oaramBroker]);
        assert(
                $requestBroker->paramBroker($key),
                isInstanceOf($brokerClass)
        );
    }

    /**
     * @test
     */
    public function returnsAddedBroker()
    {
        $paramBroker   = NewInstance::of(ParamBroker::class);
        $requestBroker = new RequestBroker(['Mock' => $paramBroker]);
        assert(
                $requestBroker->paramBroker('mock'),
                isSameAs($paramBroker)
        );
    }

    /**
     * @test
     */
    public function canOverwriteDefaultBroker()
    {
        $paramBroker   = NewInstance::of(ParamBroker::class);
        $requestBroker = new RequestBroker(['string' => $paramBroker]);
        assert(
                $requestBroker->paramBroker('string'),
                isSameAs($paramBroker)
        );
    }
}
