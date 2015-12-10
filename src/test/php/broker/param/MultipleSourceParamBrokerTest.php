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
use bovigo\callmap\NewInstance;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\lang\reflect\annotation\Annotation;
require_once __DIR__ . '/WebRequest.php';
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
        $values['paramName'] = 'foo';
        return new Annotation(
                $this->getRequestAnnotationName(),
                'SomeClass::someMethod()',
                $values,
                'Request'
        );
    }

    /**
     * returns expected value
     *
     * @return  mixed
     */
    protected abstract function expectedValue();

    /**
     * creates mocked request
     *
     * @param   mixed  $value
     * @return  \bovigo\callmap\Proxy
     */
    protected function createRequest($value)
    {
        return NewInstance::of(Request::class)
                ->mapCalls(['readParam' => ValueReader::forValue($value)]);
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function failsForUnknownSource()
    {
        $this->paramBroker->procure(
                NewInstance::of(Request::class),
                $this->createRequestAnnotation(['source' => 'foo'])
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procureParam(
                        new Param('name', ((string) $this->expectedValue())),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['source' => 'param'])
                )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $request = NewInstance::of(WebRequest::class)
                ->mapCalls(['readHeader' => ValueReader::forValue(((string) $this->expectedValue()))]);
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'header'])
                )
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
    {
        $request = NewInstance::of(WebRequest::class)
                ->mapCalls(['readCookie' => ValueReader::forValue(((string) $this->expectedValue()))]);
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'cookie'])
                )
        );
    }
}
