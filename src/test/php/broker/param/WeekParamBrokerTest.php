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
use stubbles\date\Date;
use stubbles\date\span\Week;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\WeekParamBroker.
 *
 * @since  4.5.0
 */
#[Group('broker')]
#[Group('broker_param')]
class WeekParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new WeekParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Week';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): Week
    {
        return Week::fromString('2015-W22');
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['default' => '2015-W22'])
            ),
            equals(Week::fromString('2015-W22'))
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
    public function returnsNullIfBeforeMinStartDate(): void
    {
        $currentWeek = new Week(new Date('previous monday'));
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest($currentWeek->asString()),
                $this->createRequestAnnotation(['minStartDate' => 'next monday'])
            )
        );
    }

    #[Test]
    public function returnsNullIfAfterMaxStartDate(): void
    {
        $currentWeek = new Week(new Date('tomorrow'));
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest($currentWeek->asString()),
                $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('2015-W22'),
                $this->createRequestAnnotation([
                    'minStartDate' => '2015-05-01',
                    'maxEndDate'   => '2015-05-31'
                ])
            ),
            equals(Week::fromString('2015-W22'))
        );
    }
}
