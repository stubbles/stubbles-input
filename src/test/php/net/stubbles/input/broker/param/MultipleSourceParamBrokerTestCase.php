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
use net\stubbles\lang\reflect\annotation\Annotation;
/**
 * Base tests for net\stubbles\input\broker\param\MultipleSourceParamBroker.
 */
abstract class MultipleSourceParamBrokerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ParamBroker
     */
    protected $paramBroker;

    /**
     * returns type: filter or read
     *
     * @return  string
     */
    protected abstract function getBrokerType();

    /**
     * returns broker value
     *
     * @return  mixed
     */
    protected abstract function getBrokerValue($value);

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
    protected function createRequestAnnotation(array $values)
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
     * @param   mixed  $returnValue
     * @return  \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockRequest($returnValue)
    {
        $mockRequest = $this->getMock('net\\stubbles\\input\\Request');
        $mockRequest->expects($this->once())
                    ->method($this->getBrokerType() . 'Param')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue($returnValue));
        return $mockRequest;
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
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
    public function usesParamAsDefaultSource()
    {
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($this->mockRequest($this->getBrokerValue(((string) $this->getExpectedValue()))),
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
                            $this->paramBroker->procure($this->mockRequest($this->getBrokerValue(((string) $this->getExpectedValue()))),
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
                    ->method($this->getBrokerType() . 'Header')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue($this->getBrokerValue(((string) $this->getExpectedValue()))));
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
                    ->method($this->getBrokerType() . 'Cookie')
                    ->with($this->equalTo('foo'))
                    ->will($this->returnValue($this->getBrokerValue(((string) $this->getExpectedValue()))));
        $this->assertEquals($this->getExpectedValue(),
                            $this->paramBroker->procure($mockRequest,
                                                        $this->createRequestAnnotation(array('source' => 'cookie'))
                            )
        );
    }
}
?>