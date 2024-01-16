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
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\reflect\annotation\Annotation;
use stubbles\values\Secret;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\SecretParamBroker.
 *
 * @since  3.0.0
 */
#[Group('broker')]
#[Group('broker_param')]
class SecretParamBrokerTest extends TestCase
{
    private SecretParamBroker $paramBroker;

    protected function setUp(): void
    {
        $this->paramBroker = new SecretParamBroker();
    }

    private function assertSecretEquals(string $expected, Secret $actual): void
    {
        assertThat($actual->unveil(), equals($expected));
    }

    /**
     * @param  array<string,mixed>  $values
     */
    protected function createRequestAnnotation(array $values = []): Annotation
    {
        $values['paramName'] = 'foo';
        return new Annotation(
            'Secret',
            'foo',
            array_map(fn($value): string => (string) $value, $values),
            'Request'
        );
    }

    protected function createRequest(mixed $value): Request
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
        })->throws(RuntimeException::class);
    }

    #[Test]
    public function usesParamAsDefaultSource(): void
    {
        $this->assertSecretEquals(
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
        $this->assertSecretEquals(
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
        $this->assertSecretEquals(
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
        $this->assertSecretEquals(
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
                $this->createRequestAnnotation(['required' => true])
            )
        );
    }

    #[Test]
    public function returnsNullIfShorterThanMinLength(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('Do you expect me to talk?'),
                $this->createRequestAnnotation(['minLength' => 30])
            )
        );
    }

    #[Test]
    public function returnsValueIfLongerThenMinLength(): void
    {
        $this->assertSecretEquals(
            'Do you expect me to talk?',
            $this->paramBroker->procure(
                $this->createRequest('Do you expect me to talk?'),
                $this->createRequestAnnotation(
                    ['minLength' => 10]
                )
            )
        );
    }
}
