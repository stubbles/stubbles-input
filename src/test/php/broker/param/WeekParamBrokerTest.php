<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use stubbles\date\Date;
use stubbles\date\span\Week;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\WeekParamBroker.
 *
 * @group  broker
 * @group  broker_param
 * @since  4.5.0
 */
class WeekParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new WeekParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Week';
    }

    /**
     * returns expected filtered value
     *
     * @return  \stubbles\date\span\Week
     */
    protected function expectedValue()
    {
        return Week::fromString('2015-W22');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertEquals(
                Week::fromString('2015-W22'),
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => '2015-W22'])
                )
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
        $currentMonth = new Week(new Date('tomorrow'));
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
        $currentWeek = new Week(new Date('tomorrow'));
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest($currentWeek->asString()),
                        $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        assertEquals(
                Week::fromString('2015-W22'),
                $this->paramBroker->procure(
                        $this->createRequest('2015-W22'),
                        $this->createRequestAnnotation(
                                ['minStartDate' => '2015-05-01',
                                 'maxEndDate'   => '2015-05-31'
                                ]
                        )
                )
        );
    }
}
