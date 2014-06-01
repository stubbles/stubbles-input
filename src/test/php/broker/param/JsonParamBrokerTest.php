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
 * Tests for stubbles\input\broker\param\JsonParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class JsonParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new JsonParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Json';
    }

    /**
     * returns expected filtered value
     *
     * @return  HttpUri
     */
    protected function getExpectedValue()
    {
        $phpJsonObj = new \stdClass();
        $phpJsonObj->method = 'add';
        $phpJsonObj->params = [1, 2];
        $phpJsonObj->id = 1;
        return $phpJsonObj;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest(null),
                                                        $this->createRequestAnnotation(['default' => '{"method":"add","params":[1,2],"id":1}'])
                            )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest(null),
                                                      $this->createRequestAnnotation(['required' => true])
                          )
        );
    }

    /**
     * @test
     */
    public function returnsNullForInvalidJson()
    {
        $this->assertNull($this->paramBroker->procure($this->mockRequest('{invalid'),
                                                      $this->createRequestAnnotation([])
                          )
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procureParam(new Param('name', '{"method":"add","params":[1,2],"id":1}'),
                                                             $this->createRequestAnnotation([])
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest('{"method":"add","params":[1,2],"id":1}'),
                                                        $this->createRequestAnnotation([])
                            )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest('{"method":"add","params":[1,2],"id":1}'),
                                                        $this->createRequestAnnotation(['source' => 'param'])
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
                    ->will($this->returnValue(ValueReader::forValue('{"method":"add","params":[1,2],"id":1}')));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(['source' => 'header'])
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
                    ->will($this->returnValue(ValueReader::forValue('{"method":"add","params":[1,2],"id":1}')));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(['source' => 'cookie'])
                            )
        );
    }
}
