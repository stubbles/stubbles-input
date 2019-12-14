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
 *
 * @group  broker
 * @group  broker_param
 */
class CustomDatespanParamBrokerTest extends TestCase
{
    /**
     * @var  CustomDatespanParamBroker
     */
    private $customDatespanParamBroker;

    protected function setUp(): void
    {
        $this->customDatespanParamBroker = new CustomDatespanParamBroker();
    }

    /**
     * creates filter annotation
     *
     * @param   array<string,mixed>  $values
     * @return  Annotation
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function returnsNullIfStartDateInvalid(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('invalid', '2012-04-21'),
                        $this->createRequestAnnotation(['required' => 'true'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfEndDateInvalid(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('2012-02-05', 'invalid'),
                        $this->createRequestAnnotation(['required' => 'true'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfStartDateIsMissing(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest(null, '2012-04-21'),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfEndDateIsMissing(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('2012-02-05', null),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBothDatesAreMissing(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest(null, null),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function returnsDefaultIfBothDatesAreMissingAndDefaultGiven(): void
    {
        assertThat(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest(null, null),
                        $this->createRequestAnnotation(
                                ['defaultStart' => 'yesterday',
                                 'defaultEnd'   => 'tomorrow'
                                ]
                        )
                ),
                equals(new CustomDatespan('yesterday', 'tomorrow'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBeforeMinStartDate(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('yesterday', 'today'),
                        $this->createRequestAnnotation(['minStartDate' => 'today'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxStartDate(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('today', 'tomorrow'),
                        $this->createRequestAnnotation(['maxStartDate' => 'yesterday'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfStartInRange(): void
    {
        assertThat(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('today', 'tomorrow'),
                        $this->createRequestAnnotation(
                                ['minStartDate' => 'yesterday',
                                 'maxStartDate' => 'tomorrow'
                                ]
                        )
                ),
                equals(new CustomDatespan('today', 'tomorrow'))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBeforeMinEndDate(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('yesterday', 'yesterday'),
                        $this->createRequestAnnotation(['minEndDate' => 'today'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxEndDate(): void
    {
        assertNull(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('yesterday', 'today'),
                        $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfEndInRange(): void
    {
        assertThat(
                $this->customDatespanParamBroker->procure(
                        $this->createRequest('yesterday', 'today'),
                        $this->createRequestAnnotation(
                                ['minEndDate' => 'yesterday',
                                 'maxEndDate' => 'tomorrow'
                                ]
                        )
                ),
                equals(new CustomDatespan('yesterday', 'today'))
        );
    }
}
