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
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\ArrayParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class ArrayParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new ArrayParamBroker();
    }

    /**
     * returns name of filter annotation
     *
     * @return  string
     */
    protected function getFilterAnnotationName()
    {
        return 'ArrayFilter';
    }

    /**
     * returns expected filtered value
     *
     * @return  array
     */
    protected function getExpectedFilteredValue()
    {
        return array('foo', 'bar');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals(array('foo', 'bar'),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                       $this->createFilterAnnotation(array('default' => 'foo|bar'))
                            )
        );
    }

    /**
     * @test
     */
    public function returnsValueWithDifferentSeparator()
    {
        $this->assertEquals(array('foo', 'bar'),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('foo|bar')),
                                                       $this->createFilterAnnotation(array('separator' => '|'))
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(null)),
                                                     $this->createFilterAnnotation(array('required' => true))
                          )
        );
    }

    /**
     * @test
     */
    public function returnsEmptyArrayForEmptyValue()
    {
        $this->assertEquals(array(),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('')),
                                                       $this->createFilterAnnotation(array())
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('foo, bar')),
                                                       $this->createFilterAnnotation(array())
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue('foo, bar')),
                                                       $this->createFilterAnnotation(array('source' => 'param'))
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
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($mockRequest,
                                                       $this->createFilterAnnotation(array('source' => 'header'))
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
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($mockRequest,
                                                       $this->createFilterAnnotation(array('source' => 'cookie'))
                            )
        );
    }
}
?>