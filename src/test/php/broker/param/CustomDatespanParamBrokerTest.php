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
     * instance to test
     *
     * @type  CustomDatespanParamBroker
     */
    private $customDatespanParamBroker;

    protected function setUp(): void
    {
        $this->customDatespanParamBroker = new CustomDatespanParamBroker();
    }

    /**
     * creates filter annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    private function createRequestAnnotation(array $values = []): Annotation
    {
        $values['startName'] = 'foo';
        $values['endName']   = 'bar';
        return new Annotation('CustomDatespan', 'foo', $values, 'Request');
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $startValue
     * @return  Request
     */
    private function createRequest($startValue, $endValue): Request
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
    public function returnsDatespan()
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
    public function returnsNullIfStartDateInvalid()
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
    public function returnsNullIfEndDateInvalid()
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
    public function returnsNullIfStartDateIsMissing()
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
    public function returnsNullIfEndDateIsMissing()
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
    public function returnsNullIfBothDatesAreMissing()
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
    public function returnsDefaultStartDateIfStartDateIsMissingAndDefaultGiven()
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
    public function returnsDefaultEndDateIfEndDateIsMissingAndDefaultGiven()
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
    public function returnsDefaultIfBothDatesAreMissingAndDefaultGiven()
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
    public function returnsNullIfBeforeMinStartDate()
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
    public function returnsNullIfAfterMaxStartDate()
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
    public function returnsValueIfStartInRange()
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
    public function returnsNullIfBeforeMinEndDate()
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
    public function returnsNullIfAfterMaxEndDate()
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
    public function returnsValueIfEndInRange()
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
