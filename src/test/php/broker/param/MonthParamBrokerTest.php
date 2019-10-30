<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\date\span\Month;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\MonthParamBroker.
 *
 * @group  broker
 * @group  broker_param
 * @since  4.3.0
 */
class MonthParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new MonthParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Month';
    }

    /**
     * returns expected filtered value
     *
     * @return  Month
     */
    protected function expectedValue(): Month
    {
        return new Month(2012, 04);
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => '2012-04'])
                ),
                equals(new Month(2012, 04))
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['required' => true])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBeforeMinStartDate()
    {
        $currentMonth = new Month();
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest($currentMonth->asString()),
                        $this->createRequestAnnotation(['minStartDate' => 'tomorrow'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxStartDate()
    {
        $currentMonth = new Month();
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest($currentMonth->next()->asString()),
                        $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest('2015-02'),
                        $this->createRequestAnnotation(
                                ['minStartDate' => '2015-01-01',
                                 'maxEndDate'   => '2015-03-31'
                                ]
                        )
                ),
                equals(Month::fromString('2015-02'))
        );
    }
}
