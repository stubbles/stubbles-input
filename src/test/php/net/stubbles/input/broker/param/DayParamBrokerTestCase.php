<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\input
 */
namespace net\stubbles\input\broker\param;
use stubbles\date\span\Day;
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\DayParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class DayParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new DayParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Day';
    }

    /**
     * returns expected filtered value
     *
     * @return  float
     */
    protected function getExpectedValue()
    {
        return new Day('2012-04-21');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(new Day('2012-04-21'),
                            $this->paramBroker->procure($this->mockRequest(null),
                                                        $this->createRequestAnnotation(array('default' => '2012-04-21'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(array('required' => true))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBeforeMinStartDate()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('yesterday'),
                                                      $this->createRequestAnnotation(array('minStartDate' => 'today'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxStartDate()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('today'),
                                                      $this->createRequestAnnotation(array('maxEndDate' => 'yesterday'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertEquals(new Day('today'),
                            $this->paramBroker->procure($this->mockRequest('today'),
                                                        $this->createRequestAnnotation(array('minStartDate' => 'yesterday',
                                                                                             'maxEndDate'   => 'tomorrow'
                                                                                       )
                                                        )
                            )
        );
    }
}
