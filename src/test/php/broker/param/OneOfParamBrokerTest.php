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
use stubbles\input\Param;
use stubbles\input\ValueReader;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\OneOfParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class OneOfParamBrokerTest extends MultipleSourceParamBrokerTest
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
                                                        $this->createRequestAnnotation(['allowed' => 'foo|bar',
                                                                                        'default' => 'baz'
                                                                                       ]
                                                        )
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(['allowed' => 'foo|bar',
                                                                                      'required' => true
                                                                                     ]
                                                      )
                          )
        );
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function failsForUnknownSource()
    {
        $this->paramBroker->procure($this->getMock('stubbles\input\Request'),
                                    $this->createRequestAnnotation(['source' => 'foo'])
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procureParam(new Param('name', ((string) $this->getExpectedValue())),
                                                             $this->createRequestAnnotation(['allowed' => 'foo|bar'])
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
                                                        $this->createRequestAnnotation(['allowed' => 'foo|bar'])
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
                                                        $this->createRequestAnnotation(['allowed' => 'foo|bar',
                                                                                        'source'  => 'param'
                                                                                       ]
                                                        )
                            )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $mockRequest = $this->getMock('stubbles\input\web\WebRequest');
        $mockRequest->expects($this->once())
                    ->method('readHeader')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue(((string) $this->getExpectedValue()))));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(['allowed' => 'foo|bar',
                                                                                        'source'  => 'header'
                                                                                       ]
                                                        )
                            )
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
    {
        $mockRequest = $this->getMock('stubbles\input\web\WebRequest');
        $mockRequest->expects($this->once())
                    ->method('readCookie')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue(((string) $this->getExpectedValue()))));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(['allowed' => 'foo|bar',
                                                                                        'source'  => 'cookie'
                                                                                       ]
                                                        )
                            )
        );
    }

    /**
     * @test
     * @expectedException  RuntimeException
     * @expectedExceptionMessage  No list of allowed values in annotation @Request[OneOf] on SomeClass::someMethod()
     * @since  3.0.0
     */
    public function throwsRuntimeAnnotationWhenListOfAllowedValuesIsMissing()
    {
        $this->paramBroker->procure($this->mockRequest(((string) $this->getExpectedValue())),
                                    $this->createRequestAnnotation()
        );
    }
}
