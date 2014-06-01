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
use net\stubbles\input\Param;
use net\stubbles\input\ValueReader;
require_once __DIR__ . '/MultipleSourceParamBrokerTestCase.php';
/**
 * Tests for net\stubbles\input\broker\param\OneOfParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class OneOfParamBrokerTestCase extends MultipleSourceParamBrokerTestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new OneOfParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'OneOf';
    }

    /**
     * returns expected filtered value
     *
     * @return  array
     */
    protected function getExpectedValue()
    {
        return 'foo';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals('baz',
                            $this->paramBroker->procure($this->mockRequest(null),
                                                        $this->createRequestAnnotation(array('allowed' => 'foo|bar',
                                                                                             'default' => 'baz'))
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(array('allowed' => 'foo|bar',
                                                                                           'required' => true))
                          )
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
     */
    public function failsForUnknownSource()
    {
        $this->paramBroker->procure($this->getMock('net\\stubbles\\input\\Request'),
                                    $this->createRequestAnnotation(array('source' => 'foo'))
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procureParam(new Param('name', ((string) $this->getExpectedValue())),
                                                             $this->createRequestAnnotation(array('allowed' => 'foo|bar'))
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest(((string) $this->getExpectedValue())),
                                                        $this->createRequestAnnotation(array('allowed' => 'foo|bar'))
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest(((string) $this->getExpectedValue())),
                                                        $this->createRequestAnnotation(array('allowed' => 'foo|bar',
                                                                                             'source'  => 'param'))
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
                    ->method('readHeader')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue(((string) $this->getExpectedValue()))));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(array('allowed' => 'foo|bar',
                                                                                             'source'  => 'header'))
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
                    ->method('readCookie')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue(((string) $this->getExpectedValue()))));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(array('allowed' => 'foo|bar',
                                                                                             'source'  => 'cookie'))
                            )
        );
    }
}
