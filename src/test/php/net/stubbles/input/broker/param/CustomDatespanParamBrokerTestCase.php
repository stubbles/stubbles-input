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
use net\stubbles\input\filter\ValueFilter;
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
    private function createFilterAnnotation(array $values)
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
                    ->method('filterParam')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue($startValue));
        $mockRequest->expects($this->at(1))
                    ->method('filterParam')
                    ->with($this->equalTo('bar'))
                    ->will($this->returnValue($endValue));
        return $mockRequest;
    }

    /**
     * @test
     */
    public function returnsDatespan()
    {
        $this->assertEquals(new CustomDatespan('2012-02-05', '2012-04-21'),
                            $this->customDatespanParamBroker->procure($this->mockRequest(ValueFilter::mockForValue('2012-02-05'),
                                                                                         ValueFilter::mockForValue('2012-04-21')
                                                                      ),
                                                                      $this->createFilterAnnotation(array())
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfStartDateIsMissing()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest(ValueFilter::mockForValue(null),
                                                                                       ValueFilter::mockForValue('2012-04-21')
                                                                    ),
                                                                    $this->createFilterAnnotation(array())
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfEndDateIsMissing()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest(ValueFilter::mockForValue('2012-02-05'),
                                                                                       ValueFilter::mockForValue(null)
                                                                    ),
                                                                    $this->createFilterAnnotation(array())
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfBothDatesAreMissing()
    {
        $this->assertNull($this->customDatespanParamBroker->procure($this->mockRequest(ValueFilter::mockForValue(null),
                                                                                       ValueFilter::mockForValue(null)
                                                                    ),
                                                                    $this->createFilterAnnotation(array())
                          )
        );
    }

}
?>