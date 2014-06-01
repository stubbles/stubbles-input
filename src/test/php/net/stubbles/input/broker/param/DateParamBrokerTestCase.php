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
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for stubbles\input\broker\param\DateParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class DateParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new DateParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Date';
    }

    /**
     * returns expected filtered value
     *
     * @return  float
     */
    protected function getExpectedValue()
    {
        return new Date('2012-04-21 00:00:00+02:00');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(new Date('2012-04-21'),
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
    public function returnsNullIfBeforeMinDate()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('yesterday'),
                                                      $this->createRequestAnnotation(array('minDate' => 'today'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxDate()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('today'),
                                                      $this->createRequestAnnotation(array('maxDate' => 'yesterday'))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertEquals(new Date('today'),
                            $this->paramBroker->procure($this->mockRequest('today'),
                                                        $this->createRequestAnnotation(array('minDate' => 'yesterday',
                                                                                             'maxDate'   => 'tomorrow'
                                                                                       )
                                                        )
                            )
        );
    }
}
