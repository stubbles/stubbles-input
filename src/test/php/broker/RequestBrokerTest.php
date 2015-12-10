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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\input\broker\param\ParamBroker;
use stubbles\lang\reflect;
require_once __DIR__ . '/BrokerClass.php';
/**
 * Tests for stubbles\input\broker\RequestBroker.
 *
 * @group  broker
 * @group  broker_core
 */
class RequestBrokerTest extends \PHPUnit_Framework_TestCase
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

    /**
     * set up test environment
     */
    public function setUp()
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
                reflect\annotationsOf($this->requestBroker)
                        ->contain('Singleton')
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function procureNonObjectThrowsInvalidArgumentException()
    {
        $this->requestBroker->procure($this->request, 313);
    }

    /**
     * @test
     */
    public function procuresOnlyThoseInGivenGroup()
    {
        $this->request->mapCalls(
                ['readParam' => ValueReader::forValue('just some string value')]
        );
        $object = $this->requestBroker->procure($this->request, new BrokerClass(), 'main');
        assertFalse($object->isVerbose());
        assertEquals('just some string value', $object->getBar());
        assertNull($object->getBaz());
    }

    /**
     * @test
     */
    public function procuresAllIfNoGroupGiven()
    {

        $paramBroker = NewInstance::of(ParamBroker::class)
                ->mapCalls(['procure' => 'just another string value']);
        $this->request->mapCalls(
                ['readParam' => callmap\onConsecutiveCalls(
                        ValueReader::forValue('on'),
                        ValueReader::forValue('just some string value'),
                        ValueReader::forValue('just another string value')
                    )
                ]
        );
        $requestBroker = new RequestBroker(['Mock' => $paramBroker]);
        $object = $requestBroker->procure($this->request, new BrokerClass());
        assertTrue($object->isVerbose());
        assertEquals('just some string value', $object->getBar());
        assertEquals('just another string value', $object->getBaz());
    }
}
