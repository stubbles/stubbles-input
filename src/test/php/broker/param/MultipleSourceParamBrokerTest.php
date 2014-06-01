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
use stubbles\lang\reflect\annotation\Annotation;
/**
 * Base tests for stubbles\input\broker\param\MultipleSourceParamBroker.
 */
abstract class MultipleSourceParamBrokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ParamBroker
     */
    protected $paramBroker;

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected abstract function getRequestAnnotationName();

    /**
     * creates request annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    protected function createRequestAnnotation(array $values = [])
    {
        $annotation = new Annotation($this->getRequestAnnotationName());
        $annotation->name = 'foo';
        foreach ($values as $key => $value) {
            $annotation->$key = $value;
        }

        return $annotation;
    }

    /**
     * returns expected value
     *
     * @return  mixed
     */
    protected abstract function getExpectedValue();

    /**
     * creates mocked request
     *
     * @param   mixed  $value
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockRequest($value)
    {
        $mockRequest = $this->getMock('stubbles\input\Request');
        $mockRequest->expects($this->once())
                    ->method('readParam')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue(ValueReader::forValue($value)));
        return $mockRequest;
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
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
                                                             $this->createRequestAnnotation()
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
                                                        $this->createRequestAnnotation()
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
                    ->will($this->returnValue(ValueReader::forValue(((string) $this->getExpectedValue()))));
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
                    ->will($this->returnValue(ValueReader::forValue(((string) $this->getExpectedValue()))));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(['source' => 'cookie'])
                            )
        );
    }
}
