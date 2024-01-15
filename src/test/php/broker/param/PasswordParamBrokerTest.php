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
 *
 * @group  broker
 * @group  broker_param
 */
class PasswordParamBrokerTest extends TestCase
{
    /**
     * @var  PasswordParamBroker
     */
    private $paramBroker;

    protected function setUp(): void
    {
        $this->paramBroker = new PasswordParamBroker();
    }

    /**
     * @param  string  $expectedPassword
     * @param  Secret  $actualPassword
     */
    private function assertPasswordEquals(string $expectedPassword, Secret $actualPassword): void
    {
        assertThat($actualPassword->unveil(), equals($expectedPassword));
    }

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
                'Password',
                'foo',
                array_map(function($value) { return (string) $value; }, $values),
                'Request'
        );
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $value
     * @return  Request&\bovigo\callmap\ClassProxy
     */
    protected function createRequest($value): Request
    {
        return NewInstance::of(Request::class)->returns([
                'readParam' => ValueReader::forValue($value)
        ]);
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
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired(): void
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndNotRequired(): void
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['required' => false])
                )
        );
    }

    /**
     * @test
     */
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
     * @test
     * @since  3.0.0
     */
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
