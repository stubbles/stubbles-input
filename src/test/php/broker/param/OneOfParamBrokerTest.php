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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use stubbles\input\Request;
use stubbles\input\ValueReader;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\OneOfParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
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
     */
    protected function getRequestAnnotationName(): string
    {
        return 'OneOf';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): string
    {
        return 'foo';
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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
     * @since  8.0.0
     */
    #[Test]
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

    #[Test]
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

    #[Test]
    public function failsForUnknownSource(): void
    {
        expect(function() {
            $this->paramBroker->procure(
                NewInstance::of(Request::class),
                $this->createRequestAnnotation(['source' => 'foo'])
            );
        })->throws(RuntimeException::class);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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
     * @since  3.0.0
     */
    #[Test]
    public function throwsRuntimeAnnotationWhenListOfAllowedValuesIsMissing(): void
    {
        expect(function() {
            $this->paramBroker->procure(
                $this->createRequest(((string) $this->expectedValue())),
                $this->createRequestAnnotation()
            );
        })
            ->throws(RuntimeException::class)
            ->withMessage(
                'No list of allowed values in annotation @Request[OneOf] on SomeClass::someMethod()'
            );
    }
}
