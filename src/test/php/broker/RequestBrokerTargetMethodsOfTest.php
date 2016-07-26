<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker;
use function bovigo\assert\assert;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\reflect;
require_once __DIR__ . '/BrokerClass.php';
/**
 * Tests for stubbles\input\broker\RequestBroker::targetMethodsOf().
 *
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerTargetMethodsOfTest extends \PHPUnit_Framework_TestCase
{
    public function allowedValues(): array
    {
        return [
            [new BrokerClass()],
            [BrokerClass::class],
            [reflect(BrokerClass::class)]
        ];
    }

    /**
     * @test
     * @dataProvider  allowedValues
     */
    public function returnsListOfAllMethodsWithRequestAnnotation($allowedValue)
    {
        $paramNames = [];
        foreach (RequestBroker::targetMethodsOf($allowedValue) as $targetMethod) {
            $paramNames[] = $targetMethod->paramName();
        }

        assert($paramNames, equals(['verbose', 'bar', 'baz']));
    }

    /**
     * @test
     * @dataProvider  allowedValues
     */
    public function returnsListOfAllMethodsWithRequestAnnotationInGivenGroup($allowedValue)
    {
        $paramNames = [];
        foreach (RequestBroker::targetMethodsOf($allowedValue, 'main') as $targetMethod) {
            $paramNames[] = $targetMethod->paramName();
        }

        assert($paramNames, equals(['bar']));
    }

    /**
     * @test
     */
    public function targetMethodsOfThrowsExceptionOnInvalidValue()
    {
        expect(function() {
                RequestBroker::targetMethodsOf(404);
        })->throws(\InvalidArgumentException::class);
    }
}
