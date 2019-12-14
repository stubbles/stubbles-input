<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\HttpUriParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class HttpUriParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
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
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
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
    public function returnsValueIfDnsCheckEnabledAndSuccessful(): void
    {
        assertThat(
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
    public function returnsNullIfParamNotSetAndRequired(): void
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
    public function returnsNullForInvalidHttpUri(): void
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('invalid'),
                        $this->createRequestAnnotation()
                )
        );
    }
}
