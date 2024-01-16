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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\reflect\annotation\Annotation;
use stubbles\values\Secret;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\PasswordParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class PasswordParamBrokerTest extends TestCase
{
    private PasswordParamBroker $paramBroker;

    protected function setUp(): void
    {
        $this->paramBroker = new PasswordParamBroker();
    }
    private function assertPasswordEquals(
        string $expectedPassword,
        Secret $actualPassword
    ): void {
        assertThat($actualPassword->unveil(), equals($expectedPassword));
    }

    /**
     * @param  array<string,mixed>  $values
     */
    protected function createRequestAnnotation(array $values = []): Annotation
    {
        $values['paramName'] = 'foo';
        return new Annotation(
                'Password',
                'foo',
                array_map(function($value) { return (string) $value; }, $values),
                'Request'
        );
    }

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
        $this->assertPasswordEquals(
            'topsecret',
            $this->paramBroker->procure(
                $this->createRequest('topsecret'),
                $this->createRequestAnnotation()
            )
        );
    }

    #[Test]
    public function usesParamAsSource(): void
    {
        $this->assertPasswordEquals(
            'topsecret',
            $this->paramBroker->procure(
                $this->createRequest('topsecret'),
                $this->createRequestAnnotation(['source' => 'param'])
            )
        );
    }

    #[Test]
    public function canUseHeaderAsSourceForWebRequest(): void
    {
        $request = NewInstance::of(WebRequest::class)->returns([
            'readHeader' => ValueReader::forValue('topsecret')
        ]);
        $this->assertPasswordEquals(
            'topsecret',
            $this->paramBroker->procure(
                $request,
                $this->createRequestAnnotation(['source' => 'header'])
            )
        );
    }

    #[Test]
    public function canUseCookieAsSourceForWebRequest(): void
    {
        $request =  NewInstance::of(WebRequest::class)->returns([
            'readCookie' => ValueReader::forValue('topsecret')
        ]);
        $this->assertPasswordEquals(
            'topsecret',
            $this->paramBroker->procure(
                $request,
                $this->createRequestAnnotation(['source' => 'cookie'])
            )
        );
    }

    #[Test]
    public function returnsNullIfParamNotSetAndRequired(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation()
            )
        );
    }

    #[Test]
    public function returnsNullIfParamNotSetAndNotRequired(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['required' => false])
            )
        );
    }

    #[Test]
    public function returnsNullIfTooLessMinDiffChars(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('topsecret'),
                $this->createRequestAnnotation(['minDiffChars' => 20])
            )
        );
    }

    /**
     * @since  3.0.0
     */
    #[Test]
    public function returnsNullIfTooShort(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('topsecret'),
                $this->createRequestAnnotation(['minLength' => 20])
            )
        );
    }
}
