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
use function stubbles\lang\reflect;
require_once __DIR__ . '/BrokerClass.php';
/**
 * Tests for stubbles\input\broker\RequestBroker::targetMethodsOf().
 *
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerTargetMethodsOfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function allowedValues()
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

        assertEquals(
                ['verbose', 'bar', 'baz'],
                $paramNames
        );
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

        assertEquals(
                ['bar'],
                $paramNames
        );
    }

    /**
     * @test
     * @expectedException  \InvalidArgumentException
     */
    public function targetMethodsOfThrowsExceptionOnInvalidValue()
    {
        RequestBroker::targetMethodsOf(404);
    }
}
