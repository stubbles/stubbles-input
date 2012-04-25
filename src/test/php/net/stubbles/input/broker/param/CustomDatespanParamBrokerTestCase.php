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
use net\stubbles\input\ValueReader;
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\types\datespan\CustomDatespan;
/**
 * Tests for net\stubbles\input\broker\param\CustomDatespanParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class CustomDatespanParamBrokerTestCase extends \PHPUnit_Framework_TestCase
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
    private function createRequestAnnotation(array $values)
    {
        $annotation = new Annotation('CustomDatespan');
        $annotation->startName = 'foo';
        $annotation->endName   = 'bar';
        foreach ($values as $key => $value) {
            $annotation->$key = $value;
        }

        return $annotation;
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $startValue
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockRequest($startValue, $endValue)
    {
        $mockRequest = $this->getMock('net\\stubbles\\input\\Request');
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
                                                                      $this->createRequestAnnotation(array())
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
                                                                    $this->createRequestAnnotation(array())
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
                                                                    $this->createRequestAnnotation(array())
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
                                                                    $this->createRequestAnnotation(array())
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
                                                                      $this->createRequestAnnotation(array('defaultStart' => 'today'))
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
                                                                      $this->createRequestAnnotation(array('defaultEnd' => 'today'))
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
                                                                      $this->createRequestAnnotation(array('defaultStart' => 'yesterday',
                                                                                                           'defaultEnd'   => 'tomorrow'
                                                                                                     )
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
                                                                    $this->createRequestAnnotation(array('minStartDate' => 'today'))
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
                                                                    $this->createRequestAnnotation(array('maxStartDate' => 'yesterday'))
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
                                                                      $this->createRequestAnnotation(array('minStartDate' => 'yesterday',
                                                                                                           'maxStartDate' => 'tomorrow'
                                                                                                     )
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
                                                                    $this->createRequestAnnotation(array('minEndDate' => 'today'))
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
                                                                    $this->createRequestAnnotation(array('maxEndDate' => 'yesterday'))
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
                                                                      $this->createRequestAnnotation(array('minEndDate' => 'yesterday',
                                                                                                           'maxEndDate' => 'tomorrow'
                                                                                                     )
                                                                      )
                            )
        );
    }
}
?>