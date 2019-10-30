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
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\TestCase;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\input\broker\param\ParamBroker;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\onConsecutiveCalls;
use function stubbles\reflect\annotationsOf;

require_once __DIR__ . '/BrokerClass.php';
/**
 * Tests for stubbles\input\broker\RequestBroker.
 *
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\input\broker\RequestBroker
     */
    private $requestBroker;
    /**
     * mocked request instance
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $request;

    protected function setUp(): void
    {
        $this->requestBroker = new RequestBroker();
        $this->request       = NewInstance::of(Request::class);
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        assertTrue(
                annotationsOf($this->requestBroker)->contain('Singleton')
        );
    }

    /**
     * @test
     */
    public function procureNonObjectThrowsInvalidArgumentException()
    {
        expect(function() {
                $this->requestBroker->procure($this->request, 313);
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function procuresOnlyThoseInGivenGroup()
    {
        $this->request->returns(
                ['readParam' => ValueReader::forValue('just some string value')]
        );
        $object = $this->requestBroker->procure($this->request, new BrokerClass(), 'main');
        assertFalse($object->isVerbose());
        assertThat($object->getBar(), equals('just some string value'));
        assertNull($object->getBaz());
    }

    /**
     * @test
     */
    public function procuresAllIfNoGroupGiven()
    {

        $paramBroker = NewInstance::of(ParamBroker::class)
                ->returns(['procure' => 'just another string value']);
        $this->request->returns(
                ['readParam' => onConsecutiveCalls(
                        ValueReader::forValue('on'),
                        ValueReader::forValue('just some string value'),
                        ValueReader::forValue('just another string value')
                    )
                ]
        );
        $requestBroker = new RequestBroker(['Mock' => $paramBroker]);
        $object = $requestBroker->procure($this->request, new BrokerClass());
        assertTrue($object->isVerbose());
        assertThat($object->getBar(), equals('just some string value'));
        assertThat($object->getBaz(), equals('just another string value'));
    }
}
