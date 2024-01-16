<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\HttpUriParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class HttpUriParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new HttpUriParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'HttpUri';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): HttpUri
    {
        return HttpUri::fromString('http://localhost/');
    }

    #[Test]
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

    #[Test]
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
