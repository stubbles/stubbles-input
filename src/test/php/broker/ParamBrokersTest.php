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
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stubbles\input\broker\param\ParamBroker;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
use function stubbles\reflect\annotationsOfConstructor;
/**
 * Tests for stubbles\input\broker\RequestBroker.
 */
#[Group('broker')]
#[Group('broker_core')]
class ParamBrokersTest extends TestCase
{
    private RequestBroker $requestBroker;

    protected function setUp(): void
    {
        $this->requestBroker = new RequestBroker();
    }

    #[Test]
    public function annotationsPresentOnAddParamBrokersMethod(): void
    {
        assertTrue(
            annotationsOfConstructor($this->requestBroker)->contain('Map')
        );
    }

    public static function defaultBrokerList(): Generator
    {
        foreach (RequestBroker::buildInTypes() as $name => $paramBroker) {
            yield [$name, get_class($paramBroker)];
        }
    }

    #[Test]
    #[DataProvider('defaultBrokerList')]
    public function returnsBroker(string $key, string $brokerClass): void
    {
        assertThat(
            $this->requestBroker->paramBroker($key),
            isInstanceOf($brokerClass)
        );
    }

    /**
     * @since  2.3.3
     */
    #[Test]
    #[DataProvider('defaultBrokerList')]
    #[Group('issue_45')]
    public function returnsBrokerWithLowerCaseKey(string $key, string $brokerClass): void
    {
        assertThat(
            $this->requestBroker->paramBroker(strtolower($key)),
            isInstanceOf($brokerClass)
        );
    }

    #[Test]
    public function requestUnknownParamBrokerTypeThrowsRuntimeException(): void
    {
        expect(fn() => $this->requestBroker->paramBroker('doesNotExist'))
            ->throws(RuntimeException::class);
    }

    #[Test]
    #[DataProvider('defaultBrokerList')]
    public function addingBrokersDoesNotOverrideDefaultBrokers(
        string $key,
        string $brokerClass
    ): void {
        $paramBroker   = NewInstance::of(ParamBroker::class);
        $requestBroker = new RequestBroker(['mock' => $paramBroker]);
        assertThat(
            $requestBroker->paramBroker($key),
            isInstanceOf($brokerClass)
        );
    }

    #[Test]
    public function returnsAddedBroker(): void
    {
        $paramBroker   = NewInstance::of(ParamBroker::class);
        $requestBroker = new RequestBroker(['Mock' => $paramBroker]);
        assertThat(
            $requestBroker->paramBroker('mock'),
            isSameAs($paramBroker)
        );
    }

    #[Test]
    public function canOverwriteDefaultBroker(): void
    {
        $paramBroker   = NewInstance::of(ParamBroker::class);
        $requestBroker = new RequestBroker(['string' => $paramBroker]);
        assertThat(
            $requestBroker->paramBroker('string'),
            isSameAs($paramBroker)
        );
    }
}
