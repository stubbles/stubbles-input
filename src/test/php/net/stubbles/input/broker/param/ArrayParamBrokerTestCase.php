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
require_once __DIR__ . '/MultipleSourceFilterBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\ArrayParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class ArrayParamBrokerTestCase extends MultipleSourceFilterBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new ArrayParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Array';
    }

    /**
     * returns expected filtered value
     *
     * @return  array
     */
    protected function getExpectedValue()
    {
        return array('foo', 'bar');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(array('foo', 'bar'),
                            $this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue(null)),
                                                        $this->createRequestAnnotation(array('default' => 'foo|bar'))
                            )
        );
    }

    /**
     * @test
     */
    public function returnsValueWithDifferentSeparator()
    {
        $this->assertEquals(array('foo', 'bar'),
                            $this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue('foo|bar')),
                                                        $this->createRequestAnnotation(array('separator' => '|'))
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue(null)),
                                                      $this->createRequestAnnotation(array('required' => true))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsEmptyArrayForEmptyValue()
    {
        $this->assertEquals(array(),
                            $this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue('')),
                                                        $this->createRequestAnnotation(array())
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue('foo, bar')),
                                                        $this->createRequestAnnotation(array())
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest(ValueFilter::mockForValue('foo, bar')),
                                                        $this->createRequestAnnotation(array('source' => 'param'))
                            )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $mockRequest = $this->getMock('net\\stubbles\\input\\web\WebRequest');
        $mockRequest->expects($this->once())
                    ->method('filterHeader')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueFilter::mockForValue('foo, bar')));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(array('source' => 'header'))
                            )
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
    {
        $mockRequest = $this->getMock('net\\stubbles\\input\\web\WebRequest');
        $mockRequest->expects($this->once())
                    ->method('filterCookie')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueFilter::mockForValue('foo, bar')));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(array('source' => 'cookie'))
                            )
        );
    }
}
?>