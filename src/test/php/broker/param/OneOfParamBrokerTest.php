<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use bovigo\callmap\NewInstance;
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\input\ValueReader;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\OneOfParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class OneOfParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    /**
     * @return  string[]
     */
    public static function allowedSource(): array
    {
        return ['foo', 'bar'];
    }

    protected function setUp(): void
    {
        $this->paramBroker = new OneOfParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'OneOf';
    }

    /**
     * returns expected filtered value
     *
     * @return  string
     */
    protected function expectedValue(): string
    {
        return 'foo';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
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
    public function usesDefaultFromAnnotationIfParamNotSetWithAllowedSource(): void
    {
        assertThat(
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
    public function returnsNullIfParamNotSetAndRequired(): void
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
     * @since  8.0.0
     */
    public function throwsRuntimeExceptionWhenAllowedSourceIsNoValidCallback(): void
    {
        expect(function() {
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation([
                        'allowedSource' => __CLASS__ . '::doesNotExist()',
                        'required' => true
                ])
            );
        })
        ->throws(\RuntimeException::class);
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequiredWithAllowedSource(): void
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
    public function failsForUnknownSource(): void
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
    public function canWorkWithParam(): void
    {
        assertThat(
                $this->paramBroker->procureParam(
                        new Param('name', ((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['allowed' => 'foo|bar'])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     * @deprecated  since 7.0.0, will be removed with 8.0.0
     */
    public function canWorkWithParamWithAllowedSource(): void
    {
        assertThat(
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
    public function usesParamAsDefaultSource(): void
    {
        assertThat(
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
    public function usesParamAsDefaultSourceWithAllowedSource(): void
    {
        assertThat(
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
    public function usesParamAsSource(): void
    {
        assertThat(
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
    public function usesParamAsSourceWithAllowedSource(): void
    {
        assertThat(
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
    public function canUseHeaderAsSourceForWebRequest(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
                'readHeader' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assertThat(
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
    public function canUseHeaderAsSourceForWebRequestWithAllowedSource(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
                'readHeader' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assertThat(
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
    public function canUseCookieAsSourceForWebRequest(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
                'readCookie' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assertThat(
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
    public function canUseCookieAsSourceForWebRequestWithAllowedSource(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
                'readCookie' => ValueReader::forValue(((string) $this->expectedValue()))
        ]);
        assertThat(
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
    public function throwsRuntimeAnnotationWhenListOfAllowedValuesIsMissing(): void
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
