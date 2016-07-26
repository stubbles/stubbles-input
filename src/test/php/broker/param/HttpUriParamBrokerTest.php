<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\input
 */
namespace stubbles\input\broker\param;
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
require_once __DIR__ . '/MultipleSourceParamBrokerTest.php';
/**
 * Tests for stubbles\input\broker\param\HttpUriParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class HttpUriParamBrokerTest extends MultipleSourceParamBrokerTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->paramBroker = new HttpUriParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'HttpUri';
    }

    /**
     * returns expected filtered value
     *
     * @return  HttpUri
     */
    protected function expectedValue(): HttpUri
    {
        return HttpUri::fromString('http://localhost/');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => 'http://localhost/'])
                ),
                equals($this->expectedValue())
        );
    }

    /**
     * @test
     */
    public function returnsValueIfDnsCheckEnabledAndSuccessful()
    {
        assert(
                $this->paramBroker->procure(
                        $this->createRequest('http://localhost/'),
                        $this->createRequestAnnotation(['dnsCheck' => true])
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
    public function returnsNullForInvalidHttpUri()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('invalid'),
                        $this->createRequestAnnotation()
                )
        );
    }
}
