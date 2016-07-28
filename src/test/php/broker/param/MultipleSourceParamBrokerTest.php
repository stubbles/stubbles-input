<?php
declare(strict_types=1);
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
use stubbles\reflect\annotation\Annotation;

use function bovigo\assert\assert;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
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
    protected abstract function getRequestAnnotationName(): string;

    /**
     * creates request annotation
     *
     * @param   array  $values
     * @return  Annotation
     */
    protected function createRequestAnnotation(array $values = []): Annotation
    {
        $values['paramName'] = 'foo';
        return new Annotation(
                $this->getRequestAnnotationName(),
                'SomeClass::someMethod()',
                array_map(function($value) { return (string) $value; }, $values),
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
    protected function createRequest($value): Request
    {
        return NewInstance::of(Request::class)->mapCalls([
                    'readParam' => ValueReader::forValue($value)
        ]);
    }

    /**
     * @test
     */
    public function failsForUnknownSource()
    {
        expect(function() {
                $this->paramBroker->procure(
                        NewInstance::of(Request::class),
                        $this->createRequestAnnotation(['source' => 'foo'])
                );
        })->throws(\RuntimeException::class);
    }

    /**
     * @test
     * @deprecated  since 7.0.0, will be removed with 8.0.0
     */
    public function canWorkWithParam()
    {
        assert(
                $this->paramBroker->procureParam(
                        new Param('name', ((string) $this->expectedValue())),
                        $this->createRequestAnnotation()
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation()
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['source' => 'param'])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $request = NewInstance::of(WebRequest::class)->mapCalls([
                'readHeader' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assert(
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'header'])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
    {
        $request = NewInstance::of(WebRequest::class)->mapCalls([
                'readCookie' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assert(
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'cookie'])
                ),
                equals($this->expectedValue())
        );
    }
}
