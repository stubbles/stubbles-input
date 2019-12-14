<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\FloatParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class FloatParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new FloatParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Float';
    }

    /**
     * returns expected filtered value
     *
     * @return  float
     */
    protected function expectedValue(): float
    {
        return 3.03;
    }

    /**
     * @test
     */
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
    public function returnsNullIfLowerThanMinValue(): void
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('3.03'),
                        $this->createRequestAnnotation(['minValue' => 4])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfGreaterThanMaxValue(): void
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('3.03'),
                        $this->createRequestAnnotation(['maxValue' => 3])
                )
        );
    }

    /**
     * @test
     */
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
