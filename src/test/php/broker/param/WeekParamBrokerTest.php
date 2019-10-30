<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\input\broker\param;
use stubbles\date\Date;
use stubbles\date\span\Week;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\WeekParamBroker.
 *
 * @group  broker
 * @group  broker_param
 * @since  4.5.0
 */
class WeekParamBrokerTest extends MultipleSourceParamBrokerTest
{
    protected function setUp(): void
    {
        $this->paramBroker = new WeekParamBroker();
    }

    /**
     * returns name of request annotation
     *
     * @return  string
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Week';
    }

    /**
     * returns expected filtered value
     *
     * @return  \stubbles\date\span\Week
     */
    protected function expectedValue(): Week
    {
        return Week::fromString('2015-W22');
    }

    /**
     * @test
     */
    public function usesDefaultFromAnnotationIfParamNotSet()
    {
        assertThat(
                $this->paramBroker->procure(
                        $this->createRequest(null),
                        $this->createRequestAnnotation(['default' => '2015-W22'])
                ),
                equals(Week::fromString('2015-W22'))
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
    public function returnsNullIfBeforeMinStartDate()
    {
        $currentWeek = new Week(new Date('previous monday'));
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest($currentWeek->asString()),
                        $this->createRequestAnnotation(['minStartDate' => 'next monday'])
                )
        );
    }

    /**
     * @test
     */
    public function returnsNullIfAfterMaxStartDate()
    {
        $currentWeek = new Week(new Date('tomorrow'));
        assertNull(
                $this->paramBroker->procure(
                        $this->createRequest($currentWeek->asString()),
                        $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
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
                        $this->createRequest('2015-W22'),
                        $this->createRequestAnnotation(
                                ['minStartDate' => '2015-05-01',
                                 'maxEndDate'   => '2015-05-31'
                                ]
                        )
                ),
                equals(Week::fromString('2015-W22'))
        );
    }
}
