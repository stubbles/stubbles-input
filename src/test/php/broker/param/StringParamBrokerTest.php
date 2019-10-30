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
 * Tests for stubbles\input\broker\param\StringParamBroker.
 *
 * @group  broker
 * @group  broker_param
 */
class StringParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new StringParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'String';
    }

    /**
     * returns expected filtered value
     *
     * @return  string
     */
    protected function expectedValue(): string
    {
        return 'Do you expect me to talk?';
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
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
    public function returnsNullIfShorterThanMinLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(['minLength' => 30])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfLongerThanMaxLength()
    {
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(['maxLength' => 10])
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
                        $this->createRequest('Do you expect me to talk?'),
                        $this->createRequestAnnotation(
                                ['minLength' => 10, 'maxLength' => 30]
                        )
                ),
                equals('Do you expect me to talk?')
        );
    }
}
