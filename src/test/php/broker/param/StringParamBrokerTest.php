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

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\StringParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class StringParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new StringParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'String';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): string
    {
        return 'Do you expect me to talk?';
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(
                    ['default' => 'No Mr Bond, I expect you to die!']
                )
            ),
            equals('No Mr Bond, I expect you to die!')
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
    public function returnsNullIfShorterThanMinLength(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('Do you expect me to talk?'),
                $this->createRequestAnnotation(['minLength' => 30])
            )
        );
    }

    #[Test]
    public function returnsNullIfLongerThanMaxLength(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('Do you expect me to talk?'),
                $this->createRequestAnnotation(['maxLength' => 10])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('Do you expect me to talk?'),
                $this->createRequestAnnotation(
                    ['minLength' => 10, 'maxLength' => 30]
                )
            ),
            equals('Do you expect me to talk?')
        );
    }
}
