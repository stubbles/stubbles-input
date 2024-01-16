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
 * Tests for stubbles\input\broker\param\IntegerParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class IntegerParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new IntegerParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Integer';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): int
    {
        return 303;
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['default' => 303])
            ),
            equals(303)
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
    public function returnsNullIfLowerThanMinValue(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('303'),
                $this->createRequestAnnotation(['minValue' => 400])
            )
        );
    }

    #[Test]
    public function returnsNullIfGreaterThanMaxValue(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('303'),
                $this->createRequestAnnotation(['maxValue' => 300])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('303'),
                $this->createRequestAnnotation(
                    ['minValue' => 300, 'maxValue' => 400]
                )
            ),
            equals(303)
        );
    }
}
