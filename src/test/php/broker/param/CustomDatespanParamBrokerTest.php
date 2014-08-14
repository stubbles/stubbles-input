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
use stubbles\date\span\CustomDatespan;
use stubbles\input\ValueReader;
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Tests for stubbles\input\broker\param\CustomDatespanParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class CustomDatespanParamBrokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  CustomDatespanParamBroker
     */
    private $customDatespanParamBroker;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->customDatespanParamBroker = new CustomDatespanParamBroker();
    }

    /**
     * creates filter annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    private function createRequestAnnotation(array $values = [])
    {
        $values['startName'] = 'foo';
        $values['endName']   = 'bar';
        return new Annotation('CustomDatespan', 'foo', $values, 'Request');
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $startValue
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockRequest($startValue, $endValue)
    {
        $mockRequest = $this->getMock('stubbles\input\Request');
        $mockRequest->expects($this->at(0))
                    ->method('readParam')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue($startValue)));
        $mockRequest->expects($this->at(1))
                    ->method('readParam')
                    ->with($this->equalTo('bar'))
                    ->will($this->returnValue(ValueReader::forValue($endValue)));
        return $mockRequest;
    }

    /**
     * @test
     */
    public function returnsDatespan()
    {
        $this->assertEquals(new CustomDatespan('2012-02-05', '2012-04-21'),
                            $this->customDatespanParamBroker->procure($this->mockRequest('2012-02-05',
                                                                                         '2012-04-21'
                                                                      ),
                                                                      $this->createRequestAnnotation([])
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfStartDateInvalid()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest('invalid',
                                                                                       '2012-04-21'
                                                                    ),
                                                                    $this->createRequestAnnotation(['required' => true])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfEndDateInvalid()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest('2012-02-05',
                                                                                       'invalid'
                                                                    ),
                                                                    $this->createRequestAnnotation(['required' => true])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfStartDateIsMissing()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest(null,
                                                                                       '2012-04-21'
                                                                    ),
                                                                    $this->createRequestAnnotation()
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfEndDateIsMissing()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest('2012-02-05',
                                                                                       null
                                                                    ),
                                                                    $this->createRequestAnnotation()
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBothDatesAreMissing()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest(null,
                                                                                       null
                                                                    ),
                                                                    $this->createRequestAnnotation()
                          )
        );
    }

    /**
     * @test
     */
    public function returnsDefaultStartDateIfStartDateIsMissingAndDefaultGiven()
    {
        $this->assertEquals(new CustomDatespan('today', '2012-04-21'),
                            $this->customDatespanParamBroker->procure($this->mockRequest(null,
                                                                                         '2012-04-21'
                                                                      ),
                                                                      $this->createRequestAnnotation(['defaultStart' => 'today'])
                            )
        );
    }

    /**
     * @test
     */
    public function returnsDefaultEndDateIfEndDateIsMissingAndDefaultGiven()
    {
        $this->assertEquals(new CustomDatespan('2012-02-05', 'today'),
                            $this->customDatespanParamBroker->procure($this->mockRequest('2012-02-05',
                                                                                         null
                                                                      ),
                                                                      $this->createRequestAnnotation(['defaultEnd' => 'today'])
                            )
        );
    }

    /**
     * @test
     */
    public function returnsDefaultIfBothDatesAreMissingAndDefaultGiven()
    {
        $this->assertEquals(new CustomDatespan('yesterday', 'tomorrow'),
                            $this->customDatespanParamBroker->procure($this->mockRequest(null,
                                                                                         null
                                                                      ),
                                                                      $this->createRequestAnnotation(['defaultStart' => 'yesterday',
                                                                                                      'defaultEnd'   => 'tomorrow'
                                                                                                     ]
                                                                      )
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBeforeMinStartDate()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest('yesterday',
                                                                                       'today'
                                                                    ),
                                                                    $this->createRequestAnnotation(['minStartDate' => 'today'])
                                                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxStartDate()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest('today',
                                                                                       'tomorrow'
                                                                    ),
                                                                    $this->createRequestAnnotation(['maxStartDate' => 'yesterday'])
                                                            )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfStartInRange()
    {
        $this->assertEquals(new CustomDatespan('today', 'tomorrow'),
                            $this->customDatespanParamBroker->procure($this->mockRequest('today',
                                                                                         'tomorrow'
                                                                      ),
                                                                      $this->createRequestAnnotation(['minStartDate' => 'yesterday',
                                                                                                      'maxStartDate' => 'tomorrow'
                                                                                                     ]
                                                                      )
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBeforeMinEndDate()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest('yesterday',
                                                                                       'yesterday'
                                                                    ),
                                                                    $this->createRequestAnnotation(['minEndDate' => 'today'])
                                                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxEndDate()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest('yesterday',
                                                                                       'today'
                                                                    ),
                                                                    $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
                                                            )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfEndInRange()
    {
        $this->assertEquals(new CustomDatespan('yesterday', 'today'),
                            $this->customDatespanParamBroker->procure($this->mockRequest('yesterday',
                                                                                         'today'
                                                                      ),
                                                                      $this->createRequestAnnotation(['minEndDate' => 'yesterday',
                                                                                                      'maxEndDate' => 'tomorrow'
                                                                                                     ]
                                                                      )
                            )
        );
    }
}
