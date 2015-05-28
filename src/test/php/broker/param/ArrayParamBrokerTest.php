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
use stubbles\input\ValueReader;
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
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new ArrayParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Array';
    }

    /**
     * returns expected filtered value
     *
     * @return  array
     */
    protected function expectedValue()
    {
        return ['foo', 'bar'];
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => 'foo|bar'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueWithDifferentSeparator()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest('foo|bar'),
                        $this->createRequestAnnotation(['separator' => '|'])
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
    public function returnsEmptyArrayForEmptyValue()
    {
        assertEquals(
                [],
                $this->paramBroker->procure(
                        $this->createRequest(''),
                        $this->createRequestAnnotation([])
                )
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procureParam(
                        new Param('name', 'foo, bar'),
                        $this->createRequestAnnotation([])
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSource()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest('foo, bar'),
                        $this->createRequestAnnotation([])
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSource()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest('foo, bar'),
                        $this->createRequestAnnotation(['source' => 'param'])
                )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $request = NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readHeader' => ValueReader::forValue('foo, bar')]);
        assertEquals(
                $this->expectedValue(),
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
        $request = NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readCookie' => ValueReader::forValue('foo, bar')]);
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'cookie'])
                )
        );
    }
}
