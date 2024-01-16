<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\reflect;
/**
 * Tests for stubbles\input\broker\RequestBroker::targetMethodsOf().
 */
#[Group('broker')]
#[Group('broker_core')]
class RequestBrokerTargetMethodsOfTest extends TestCase
{
    /**
     * @return  array<mixed[]>
     */
    public static function allowedValues(): array
    {
        return [
            [new BrokerClass()],
            [BrokerClass::class],
            [reflect(BrokerClass::class)]
        ];
    }

    /**
     * @param  object|class-string<object>|ReflectionClass<object>  $allowedValue
     */
    #[Test]
    #[DataProvider('allowedValues')]
    public function returnsListOfAllMethodsWithRequestAnnotation(
        string|object $allowedValue
    ): void {
        $paramNames = RequestBroker::targetMethodsOf($allowedValue)
            ->map(fn(TargetMethod $targetMethod): string => $targetMethod->paramName())
            ->values();

        assertThat($paramNames, equals(['verbose', 'bar', 'baz']));
    }

    /**
     * @param  object|class-string<object>|ReflectionClass<object>  $allowedValue
     */
    #[Test]
    #[DataProvider('allowedValues')]
    public function returnsListOfAllMethodsWithRequestAnnotationInGivenGroup(
        string|object $allowedValue
    ): void {
        $paramNames = RequestBroker::targetMethodsOf($allowedValue, 'main')
            ->map(fn(TargetMethod $targetMethod): string => $targetMethod->paramName())
            ->values();

        assertThat($paramNames, equals(['bar']));
    }
}
