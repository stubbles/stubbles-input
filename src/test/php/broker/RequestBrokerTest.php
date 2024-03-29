<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker;

use bovigo\callmap\ClassProxy;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\input\broker\param\ParamBroker;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\onConsecutiveCalls;
use function stubbles\reflect\annotationsOf;
/**
 * Tests for stubbles\input\broker\RequestBroker.
 */
#[Group('broker')]
#[Group('broker_core')]
class RequestBrokerTest extends TestCase
{
    private RequestBroker $requestBroker;
    private Request&ClassProxy $request;

    protected function setUp(): void
    {
        $this->requestBroker = new RequestBroker();
        $this->request       = NewInstance::of(Request::class);
    }

    #[Test]
    public function annotationsPresentOnClass(): void
    {
        assertTrue(
            annotationsOf($this->requestBroker)->contain('Singleton')
        );
    }

    #[Test]
    public function procuresOnlyThoseInGivenGroup(): void
    {
        $this->request->returns(
            ['readParam' => ValueReader::forValue('just some string value')]
        );
        $object = new BrokerClass();
        $this->requestBroker->procure($this->request, $object, 'main');

        assertFalse($object->isVerbose());
        assertThat($object->getBar(), equals('just some string value'));
        assertNull($object->getBaz());
    }

    #[Test]
    public function procuresAllIfNoGroupGiven(): void
    {

        $paramBroker = NewInstance::of(ParamBroker::class)
            ->returns(['procure' => 'just another string value']);
        $this->request->returns([
            'readParam' => onConsecutiveCalls(
                ValueReader::forValue('on'),
                ValueReader::forValue('just some string value'),
                ValueReader::forValue('just another string value')
            )
        ]);
        $requestBroker = new RequestBroker(['Mock' => $paramBroker]);
        $object = new BrokerClass();
        $requestBroker->procure($this->request, $object);

        assertTrue($object->isVerbose());
        assertThat($object->getBar(), equals('just some string value'));
        assertThat($object->getBaz(), equals('just another string value'));
    }
}
