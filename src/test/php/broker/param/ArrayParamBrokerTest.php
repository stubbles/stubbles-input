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
use stubbles\input\ValueReader;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
require_once __DIR__ . '/WebRequest.php';
/**
 * Tests for stubbles\input\broker\param\ArrayParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class ArrayParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new ArrayParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Array';
    }

    /**
     * returns expected filtered value
     *
     * @return  array
     */
    protected function expectedValue(): array
    {
        return ['foo', 'bar'];
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => 'foo|bar'])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function returnsValueWithDifferentSeparator()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest('foo|bar'),
                        $this->createRequestAnnotation(['separator' => '|'])
                ),
                equals($this->expectedValue())
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
    public function returnsEmptyArrayForEmptyValue()
    {
        assertEmptyArray(
                $this->paramBroker->procure(
                        $this->createRequest(''),
                        $this->createRequestAnnotation([])
                )
        );
    }

    /**
     * @test
     * @deprecated  since 7.0.0, will be removed with 8.0.0
     */
    public function canWorkWithParam()
    {
        assertThat(
                $this->paramBroker->procureParam(
                        new Param('name', 'foo, bar'),
                        $this->createRequestAnnotation([])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest('foo, bar'),
                        $this->createRequestAnnotation([])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest('foo, bar'),
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
        $request = NewInstance::of(WebRequest::class)->returns([
                'readHeader' => ValueReader::forValue('foo, bar')
        ]);
        assertThat(
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
        $request = NewInstance::of(WebRequest::class)->returns([
                'readCookie' => ValueReader::forValue('foo, bar')
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
