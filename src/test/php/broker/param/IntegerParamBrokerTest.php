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
 * Tests for stubbles\input\broker\param\IntegerParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class IntegerParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new IntegerParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Integer';
    }

    /**
     * returns expected filtered value
     *
     * @return  int
     */
    protected function expectedValue(): int
    {
        return 303;
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => 303])
                ),
                equals(303)
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
    public function returnsNullIfLowerThanMinValue()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('303'),
                        $this->createRequestAnnotation(['minValue' => 400])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfGreaterThanMaxValue()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('303'),
                        $this->createRequestAnnotation(['maxValue' => 300])
                )
        );
    }

    /**
     * @test
     */
    public function returnsValueIfInRange()
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
