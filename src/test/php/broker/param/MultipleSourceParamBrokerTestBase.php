<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use bovigo\callmap\ClassProxy;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\reflect\annotation\Annotation;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Base tests for stubbles\input\broker\param\MultipleSourceParamBroker.
 */
abstract class MultipleSourceParamBrokerTestBase extends TestCase
{
    protected ParamBroker $paramBroker;

    /**
     * returns name of request annotation
     */
    abstract protected function getRequestAnnotationName(): string;

    /**
     * creates request annotation
     *
     * @param   array<string,mixed>  $values
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
     */
    abstract protected function expectedValue(): mixed;

    /**
     * creates mocked request
     */
    protected function createRequest(mixed $value): Request&ClassProxy
    {
        return NewInstance::of(Request::class)->returns([
            'readParam' => ValueReader::forValue($value)
        ]);
    }

    #[Test]
    public function failsForUnknownSource(): void
    {
        expect(function() {
            $this->paramBroker->procure(
                NewInstance::of(Request::class),
                $this->createRequestAnnotation(['source' => 'foo'])
            );
        })->throws(\RuntimeException::class);
    }

    #[Test]
    public function usesParamAsDefaultSource(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(((string) $this->expectedValue())),
                $this->createRequestAnnotation()
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
                $this->createRequestAnnotation(['source' => 'param'])
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
                $this->createRequestAnnotation(['source' => 'header'])
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
                $this->createRequestAnnotation(['source' => 'cookie'])
            ),
            equals($this->expectedValue())
        );
    }
}
