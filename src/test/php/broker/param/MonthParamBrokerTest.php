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
use stubbles\date\span\Month;
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
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new MonthParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Month';
    }

    /**
     * returns expected filtered value
     *
     * @return  float
     */
    protected function getExpectedValue()
    {
        return new Month(2012, 04);
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(
                new Month(2012, 04),
                $this->paramBroker->procure(
                        $this->mockRequest(null),
                        $this->createRequestAnnotation(['default' => '2012-04'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull(
                $this->paramBroker->procure(
                        $this->mockRequest(null),
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
        $this->assertNull(
                $this->paramBroker->procure(
                        $this->mockRequest($currentMonth->asString()),
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
        $this->assertNull(
                $this->paramBroker->procure(
                        $this->mockRequest($currentMonth->next()->asString()),
                        $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertEquals(
                Month::fromString('2015-02'),
                $this->paramBroker->procure(
                        $this->mockRequest('2015-02'),
                        $this->createRequestAnnotation(
                                ['minStartDate' => '2015-01-01',
                                 'maxEndDate'   => '2015-03-31'
                                ]
                        )
                )
        );
    }
}
