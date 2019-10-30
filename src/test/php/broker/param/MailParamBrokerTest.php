<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use function bovigo\assert\assertNull;
/**
 * Tests for stubbles\input\broker\param\MailParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class MailParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new MailParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Mail';
    }

    /**
     * returns expected filtered value
     *
     * @return  string
     */
    protected function expectedValue(): string
    {
        return 'me@example.com';
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
}
