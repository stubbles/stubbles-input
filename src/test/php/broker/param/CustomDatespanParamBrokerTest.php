<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\date\span\CustomDatespan;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\reflect\annotation\Annotation;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\onConsecutiveCalls;
/**
 * Tests for stubbles\input\broker\param\CustomDatespanParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class CustomDatespanParamBrokerTest extends TestCase
{
    private CustomDatespanParamBroker $customDatespanParamBroker;

    protected function setUp(): void
    {
        $this->customDatespanParamBroker = new CustomDatespanParamBroker();
    }

    /**
     * creates filter annotation
     *
     * @param array<string,mixed>  $values
     */
    private function createRequestAnnotation(array $values = []): Annotation
    {
        $values['startName'] = 'foo';
        $values['endName']   = 'bar';
        return new Annotation('CustomDatespan', 'foo', $values, 'Request');
    }

    private function createRequest(?string $startValue, ?string $endValue): Request
    {
        return NewInstance::of(Request::class)
            ->returns(['readParam' => onConsecutiveCalls(
                ValueReader::forValue($startValue),
                ValueReader::forValue($endValue)
            )]
        );
    }

    #[Test]
    public function returnsDatespan(): void
    {
        assertThat(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('2012-02-05', '2012-04-21'),
                $this->createRequestAnnotation([])
            ),
            equals(new CustomDatespan('2012-02-05', '2012-04-21'))
        );
    }

    #[Test]
    public function returnsNullIfStartDateInvalid(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('invalid', '2012-04-21'),
                $this->createRequestAnnotation(['required' => 'true'])
            )
        );
    }

    #[Test]
    public function returnsNullIfEndDateInvalid(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('2012-02-05', 'invalid'),
                $this->createRequestAnnotation(['required' => 'true'])
            )
        );
    }

    #[Test]
    public function returnsNullIfStartDateIsMissing(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest(null, '2012-04-21'),
                $this->createRequestAnnotation()
            )
        );
    }

    #[Test]
    public function returnsNullIfEndDateIsMissing(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('2012-02-05', null),
                $this->createRequestAnnotation()
            )
        );
    }

    #[Test]
    public function returnsNullIfBothDatesAreMissing(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest(null, null),
                $this->createRequestAnnotation()
            )
        );
    }

    #[Test]
    public function returnsDefaultStartDateIfStartDateIsMissingAndDefaultGiven(): void
    {
        assertThat(
            $this->customDatespanParamBroker->procure(
                $this->createRequest(null, '2012-04-21'),
                $this->createRequestAnnotation(['defaultStart' => 'today'])
            ),
            equals(new CustomDatespan('today', '2012-04-21'))
        );
    }

    #[Test]
    public function returnsDefaultEndDateIfEndDateIsMissingAndDefaultGiven(): void
    {
        assertThat(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('2012-02-05', null),
                $this->createRequestAnnotation(['defaultEnd' => 'today'])
            ),
            equals(new CustomDatespan('2012-02-05', 'today'))
        );
    }

    #[Test]
    public function returnsDefaultIfBothDatesAreMissingAndDefaultGiven(): void
    {
        assertThat(
            $this->customDatespanParamBroker->procure(
                $this->createRequest(null, null),
                $this->createRequestAnnotation([
                    'defaultStart' => 'yesterday',
                    'defaultEnd'   => 'tomorrow'
                ])
            ),
            equals(new CustomDatespan('yesterday', 'tomorrow'))
        );
    }

    #[Test]
    public function returnsNullIfBeforeMinStartDate(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('yesterday', 'today'),
                $this->createRequestAnnotation(['minStartDate' => 'today'])
            )
        );
    }

    #[Test]
    public function returnsNullIfAfterMaxStartDate(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('today', 'tomorrow'),
                $this->createRequestAnnotation(['maxStartDate' => 'yesterday'])
            )
        );
    }

    #[Test]
    public function returnsValueIfStartInRange(): void
    {
        assertThat(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('today', 'tomorrow'),
                $this->createRequestAnnotation([
                    'minStartDate' => 'yesterday',
                    'maxStartDate' => 'tomorrow'
                ])
            ),
            equals(new CustomDatespan('today', 'tomorrow'))
        );
    }

    #[Test]
    public function returnsNullIfBeforeMinEndDate(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('yesterday', 'yesterday'),
                $this->createRequestAnnotation(['minEndDate' => 'today'])
            )
        );
    }

    #[Test]
    public function returnsNullIfAfterMaxEndDate(): void
    {
        assertNull(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('yesterday', 'today'),
                $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
            )
        );
    }

    #[Test]
    public function returnsValueIfEndInRange(): void
    {
        assertThat(
            $this->customDatespanParamBroker->procure(
                $this->createRequest('yesterday', 'today'),
                $this->createRequestAnnotation([
                    'minEndDate' => 'yesterday',
                    'maxEndDate' => 'tomorrow'
                ])
            ),
            equals(new CustomDatespan('yesterday', 'today'))
        );
    }
}
