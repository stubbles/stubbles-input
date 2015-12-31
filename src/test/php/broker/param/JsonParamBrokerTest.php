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

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
require_once __DIR__ . '/WebRequest.php';
/**
 * Tests for stubbles\input\broker\param\JsonParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class JsonParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new JsonParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'Json';
    }

    /**
     * returns expected filtered value
     *
     * @return  HttpUri
     */
    protected function expectedValue()
    {
        $phpJsonObj = new \stdClass();
        $phpJsonObj->method = 'add';
        $phpJsonObj->params = [1, 2];
        $phpJsonObj->id = 1;
        return $phpJsonObj;
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
                                ['default' => '{"method":"add","params":[1,2],"id":1}']
                        )
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
    public function returnsNullForInvalidJson()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('{invalid'),
                        $this->createRequestAnnotation([])
                )
        );
    }

    /**
     * @test
     */
    public function canWorkWithParam()
    {
        assert(
                $this->paramBroker->procureParam(
                        new Param('name', '{"method":"add","params":[1,2],"id":1}'),
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
        assert(
                $this->paramBroker->procure(
                        $this->createRequest('{"method":"add","params":[1,2],"id":1}'),
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
        assert(
                $this->paramBroker->procure(
                        $this->createRequest('{"method":"add","params":[1,2],"id":1}'),
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
        $request = NewInstance::of(WebRequest::class)->mapCalls([
                'readHeader' => ValueReader::forValue('{"method":"add","params":[1,2],"id":1}')
        ]);
        assert(
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
        $request = NewInstance::of(WebRequest::class)->mapCalls([
                'readCookie' => ValueReader::forValue('{"method":"add","params":[1,2],"id":1}')
        ]);
        assert(
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(['source' => 'cookie'])
                ),
                equals($this->expectedValue())
        );
    }
}
