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
 * Tests for stubbles\input\broker\param\OneOfParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class OneOfParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * @return  string[]
     */
    public static function allowedSource()
    {
        return ['foo', 'bar'];
    }

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new OneOfParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName()
    {
        return 'OneOf';
    }

    /**
     * returns expected filtered value
     *
     * @return  array
     */
    protected function expectedValue()
    {
        return 'foo';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertEquals(
                'baz',
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'default' => 'baz']
                        )
                )
        );
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSetWithAllowedSource()
    {
        assertEquals(
                'baz',
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(
                                ['allowedSource' => 'stubbles\input\broker\param\OneOfParamBrokerTest::allowedSource()', 'default' => 'baz']
                        )
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
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'required' => true]
                        )
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfParamNotSetAndRequiredWithAllowedSource()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(
                                ['allowedSource' => 'stubbles\input\broker\param\OneOfParamBrokerTest::allowedSource()', 'required' => true]
                        )
                )
        );
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function failsForUnknownSource()
    {
        $this->paramBroker->procure(
                NewInstance::of('stubbles\input\Request'),
                $this->createRequestAnnotation(['source' => 'foo'])
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
                        new Param('name', ((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['allowed' => 'foo|bar'])
                )
        );
    }

    /**
     * @test
     */
    public function canWorkWithParamWithAllowedSource()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procureParam(
                        new Param('name', ((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['allowedSource' => 'stubbles\input\broker\param\OneOfParamBrokerTest::allowedSource()'])
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
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['allowed' => 'foo|bar'])
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsDefaultSourceWithAllowedSource()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation(['allowedSource' => 'stubbles\input\broker\param\OneOfParamBrokerTest::allowedSource()'])
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
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'source'  => 'param']
                        )
                )
        );
    }

    /**
     * @test
     */
    public function usesParamAsSourceWithAllowedSource()
    {
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $this->createRequest(((string) $this->expectedValue())),
                        $this->createRequestAnnotation(
                                ['allowedSource' => 'stubbles\input\broker\param\OneOfParamBrokerTest::allowedSource()', 'source'  => 'param']
                        )
                )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequest()
    {
        $request = NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readHeader' => ValueReader::forValue(((string) $this->expectedValue()))]);
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'source'  => 'header']
                        )
                )
        );
    }

    /**
     * @test
     */
    public function canUseHeaderAsSourceForWebRequestWithAllowedSource()
    {
        $request = NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readHeader' => ValueReader::forValue(((string) $this->expectedValue()))]);
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(
                                ['allowedSource' => 'stubbles\input\broker\param\OneOfParamBrokerTest::allowedSource()', 'source'  => 'header']
                        )
                )
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequest()
    {
        $request = NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readCookie' => ValueReader::forValue(((string) $this->expectedValue()))]);
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(
                                ['allowed' => 'foo|bar', 'source'  => 'cookie']
                        )
                )
        );
    }

    /**
     * @test
     */
    public function canUseCookieAsSourceForWebRequestWithAllowedSource()
    {
        $request = NewInstance::of('stubbles\input\broker\param\WebRequest')
                ->mapCalls(['readCookie' => ValueReader::forValue(((string) $this->expectedValue()))]);
        assertEquals(
                $this->expectedValue(),
                $this->paramBroker->procure(
                        $request,
                        $this->createRequestAnnotation(
                                ['allowedSource' => 'stubbles\input\broker\param\OneOfParamBrokerTest::allowedSource()', 'source'  => 'cookie']
                        )
                )
        );
    }

    /**
     * @test
     * @expectedException  RuntimeException
     * @expectedExceptionMessage  No list of allowed values in annotation @Request[OneOf] on SomeClass::someMethod()
     * @since  3.0.0
     */
    public function throwsRuntimeAnnotationWhenListOfAllowedValuesIsMissing()
    {
        $this->paramBroker->procure(
                $this->createRequest(((string) $this->expectedValue())),
                $this->createRequestAnnotation()
        );
    }
}
