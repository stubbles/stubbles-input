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

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
require_once __DIR__ . '/WebRequest.php';
/**
 * Tests for stubbles\input\broker\param\OneOfParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class OneOfParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * @return  string[]
     */
    public static function allowedSource()
    {
        return ['foo', 'bar'];
    }

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
    protected function expectedValue()
    {
        return 'foo';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'default' => 'baz']
                        )
                ),
                equals('baz')
        );
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSetWithAllowedSource()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation([
                                'allowedSource' => __CLASS__ . '::allowedSource()',
                                'default' => 'baz'
                        ])
                ),
                equals('baz')
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'required' => true]
                        )
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequiredWithAllowedSource()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation([
                                'allowedSource' => __CLASS__ . '::allowedSource()',
                                'required' => true
                        ])
                )
        );
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
     */
    public function canWorkWithParam()
    {
        assert(
                $this->paramBroker->procureParam(
                        new Param('name', ((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['allowed' => 'foo|bar'])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function canWorkWithParamWithAllowedSource()
    {
        assert(
                $this->paramBroker->procureParam(
                        new Param('name', ((string) $this->expectedValue())),
                        $this->createRequestAnnotation([
                                'allowedSource' => __CLASS__ . '::allowedSource()'
                        ])
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
                        $this->createRequestAnnotation(['allowed' => 'foo|bar'])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSourceWithAllowedSource()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation([
                                'allowedSource' => __CLASS__ . '::allowedSource()'
                        ])
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
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'source'  => 'param']
                        )
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function usesParamAsSourceWithAllowedSource()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation([
                                'allowedSource' => __CLASS__ . '::allowedSource()',
                                'source'  => 'param'
                        ])
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
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'source'  => 'header']
                        )
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequestWithAllowedSource()
    {
        $request = NewInstance::of(WebRequest::class)->mapCalls([
                'readHeader' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assert(
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation([
                                'allowedSource' => __CLASS__ . '::allowedSource()',
                                'source'  => 'header'
                        ])
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
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'source'  => 'cookie']
                        )
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequestWithAllowedSource()
    {
        $request = NewInstance::of(WebRequest::class)->mapCalls([
                'readCookie' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assert(
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation([
                                'allowedSource' => __CLASS__ . '::allowedSource()',
                                'source'  => 'cookie'
                        ])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function throwsRuntimeAnnotationWhenListOfAllowedValuesIsMissing()
    {
        expect(function() {
            $this->paramBroker->procure(
                    $this->createRequest(((string) $this->expectedValue())),
                    $this->createRequestAnnotation()
            );
        })
        ->throws(\RuntimeException::class)
        ->withMessage('No list of allowed values in annotation @Request[OneOf] on SomeClass::someMethod()');
    }
}
