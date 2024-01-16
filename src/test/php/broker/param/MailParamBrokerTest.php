<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;

use PHPUnit\Framework\Attributes\Test;

use function bovigo\assert\assertNull;
/**
 * Tests for stubbles\input\broker\param\MailParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class MailParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new MailParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Mail';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): string
    {
        return 'me@example.com';
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
}
