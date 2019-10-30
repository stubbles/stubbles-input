<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\TestCase;
use stubbles\input\broker\param\ParamBroker;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
use function stubbles\reflect\annotationsOfConstructor;
/**
 * Tests for stubbles\input\broker\RequestBroker.
 *
 * @group  broker
 * @group  broker_core
 */
class ParamBrokersTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\broker\RequestBroker
     */
    private $requestBroker;

    protected function setUp(): void
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

    public function defaultBrokerList(): array
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
        assertThat(
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
        assertThat(
                $this->requestBroker->paramBroker(strtolower($key)),
                isInstanceOf($brokerClass)
        );
    }

    /**
     * @test
     */
    public function requestUnknownParamBrokerTypeThrowsRuntimeException()
    {
        expect(function() {
                $this->requestBroker->paramBroker('doesNotExist');
        })->throws(\RuntimeException::class);
    }

    /**
     * @test
     * @dataProvider  defaultBrokerList
     */
    public function addingBrokersDoesNotOverrideDefaultBrokers($key, $brokerClass)
    {
        $oaramBroker   = NewInstance::of(ParamBroker::class);
        $requestBroker = new RequestBroker(['mock' => $oaramBroker]);
        assertThat(
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
        assertThat(
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
        assertThat(
                $requestBroker->paramBroker('string'),
                isSameAs($paramBroker)
        );
    }
}
