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
use stubbles\date\span\Month;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\input\broker\param\MonthParamBroker.
 *
 * @since  4.3.0
 */
#[Group('broker')]
#[Group('broker_param')]
class MonthParamBrokerTest extends MultipleSourceParamBrokerTestBase
{
    protected function setUp(): void
    {
        $this->paramBroker = new MonthParamBroker();
    }

    /**
     * returns name of request annotation
     */
    protected function getRequestAnnotationName(): string
    {
        return 'Month';
    }

    /**
     * returns expected filtered value
     */
    protected function expectedValue(): Month
    {
        return new Month(2012, 04);
    }

    #[Test]
    public function usesDefaultFromAnnotationIfParamNotSet(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest(null),
                $this->createRequestAnnotation(['default' => '2012-04'])
            ),
            equals(new Month(2012, 04))
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
        $currentMonth = new Month();
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest($currentMonth->asString()),
                $this->createRequestAnnotation(['minStartDate' => 'tomorrow'])
            )
        );
    }

    #[Test]
    public function returnsNullIfAfterMaxStartDate(): void
    {
        $currentMonth = new Month();
        assertNull(
            $this->paramBroker->procure(
                $this->createRequest($currentMonth->next()->asString()),
                $this->createRequestAnnotation(['maxEndDate' => 'yesterday'])
            )
        );
    }

    #[Test]
    public function returnsValueIfInRange(): void
    {
        assertThat(
            $this->paramBroker->procure(
                $this->createRequest('2015-02'),
                $this->createRequestAnnotation([
                    'minStartDate' => '2015-01-01',
                    'maxEndDate'   => '2015-03-31'
                ])
            ),
            equals(Month::fromString('2015-02'))
        );
    }
}
