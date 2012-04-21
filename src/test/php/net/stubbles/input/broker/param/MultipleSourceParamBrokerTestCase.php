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
     * returns name of filter annotation
     *
     * @return  string
     */
    protected abstract function getFilterAnnotationName();

    /**
     * creates filter annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    protected function createFilterAnnotation(array $values)
    {
        $annotation = new Annotation($this->getFilterAnnotationName());
        $annotation->fieldName = 'foo';
        foreach ($values as $key => $value) {
            $annotation->$key = $value;
        }

        return $annotation;
    }

    /**
     * returns expected filtered value
     *
     * @return  mixed
     */
    protected abstract function getExpectedFilteredValue();

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
                    ->method('filterParam')
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
        $this->paramBroker->handle($this->getMock('net\\stubbles\\input\\Request'),
                                   $this->createFilterAnnotation(array('source' => 'foo'))
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(((string) $this->getExpectedFilteredValue()))),
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
                            $this->paramBroker->handle($this->mockRequest(ValueFilter::mockForValue(((string) $this->getExpectedFilteredValue()))),
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
                    ->will($this->returnValue(ValueFilter::mockForValue(((string) $this->getExpectedFilteredValue()))));
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
                    ->will($this->returnValue(ValueFilter::mockForValue(((string) $this->getExpectedFilteredValue()))));
        $this->assertEquals($this->getExpectedFilteredValue(),
                            $this->paramBroker->handle($mockRequest,
                                                       $this->createFilterAnnotation(array('source' => 'cookie'))
                            )
        );
    }
}
?>