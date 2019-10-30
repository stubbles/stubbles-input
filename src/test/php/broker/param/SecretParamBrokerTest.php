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
use stubbles\input\Param;
use stubbles\input\Request;
use stubbles\input\ValueReader;
use stubbles\reflect\annotation\Annotation;
use stubbles\values\Secret;


use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/WebRequest.php';
/**
 * Tests for stubbles\input\broker\param\SecretParamBroker.
 *
 * @group  broker
 * @group  broker_param
 * @since  3.0.0
 */
class SecretParamBrokerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->paramBroker = new SecretParamBroker();
    }

    /**
     * @param  string  $expected
     * @param  Secret  $actual
     */
    private function assertSecretEquals($expected, Secret $actual)
    {
        assertThat($actual->unveil(), equals($expected));
    }

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
                'Secret',
                'foo',
                array_map(function($value) { return (string) $value; }, $values),
                'Request'
        );
    }

    /**
     * creates mocked request
     *
     * @param   mixed  $value
     * @return  Request
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
        $this->assertSecretEquals(
                'topsecret',
                $this->paramBroker->procureParam(
                        new Param('name', 'topsecret'),
                        $this->createRequestAnnotation()
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        $this->assertSecretEquals(
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
    public function usesParamAsSource()
    {
        $this->assertSecretEquals(
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
    public function canUseHeaderAsSourceForWebRequest()
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

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
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

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequired()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['required' => true])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfShorterThanMinLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(['minLength' => 30])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfLongerThanMaxLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(['maxLength' => 10])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
    {
        $this->assertSecretEquals(
                'Do you expect me to talk?',
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(
                                ['minLength' => 10, 'maxLength' => 30]
                        )
                )
        );
    }
}
