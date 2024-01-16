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
 * Tests for stubbles\input\broker\param\FloatParamBroker.
 */
#[Group('broker')]
#[Group('broker_param')]
class FloatParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new FloatParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Float';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): float
    {
        return 3.03;
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['default' => 3.03])
            ),
            equals(3.03)
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
                $this->createRequest('3.03'),
                $this->createRequestAnnotation(['minValue' => 4])
            )
        );
    }

    #[Test]
    public function returnsNullIfGreaterThanMaxValue(): void
    {
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest('3.03'),
                $this->createRequestAnnotation(['maxValue' => 3])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('3.03'),
                $this->createRequestAnnotation(
                    ['minValue' => 3, 'maxValue' => 4]
                )
            ),
            equals(3.03)
        );
    }
}
